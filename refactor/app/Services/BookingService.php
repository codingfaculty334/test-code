<?php

namespace App\Services;

use DTApi\Repository\BookingRepository;
use App\Contracts\IBookingServiceContract;

/**
 * Class BookingService
 *
 * @package App\Services
 */
class BookingService implements IBookingServiceContract
{

    /**
     * Booking Repo Instance
     *
     * @var BookingRepository
     */
    private $_bookingRepo;

    /**
     * BookingService constructor.
     *
     * @param BookingRepository $_bookingRepo
     */
    public function __construct(BookingRepository $_bookingRepo)
    {
        $this->_bookingRepo = $_bookingRepo;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $response = null;
        $config = config('app_config.roles');

        if($user_id = $request->get('user_id')) {

            $response = $this->_bookingRepo->getUsersJobs($user_id);

        } elseif($request->__authenticatedUser->user_type == $config['admin'] || $request->__authenticatedUser->user_type == $config['super_admin']) {
            $response = $this->_bookingRepo->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return response($this->_bookingRepo->with('translatorJobRel.user')->find($id) ?? []);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();
        return response($this->_bookingRepo->store($request->__authenticatedUser, $data) ?? []);
    }

    /**
     * Update Booking
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $response = $this->_bookingRepo->updateJob($id, array_except($data, ['_token', 'submit']), $request->__authenticatedUser);
        return response($response ?? []);
    }

    /**
     * Get History
     *
     * @param Request $request
     *
     * @return null
     */
    public function getHistory(Request $request)
    {
        $user_id = $request->get('user_id');
        return $user_id
            ? response($this->repository->getUsersJobsHistory($user_id, $request))
            : null;
    }

    /**
     * Job Email
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $data = $request->all();
        $response = $this->_bookingRepo->storeJobEmail($data);
        return response($response);
    }

    /**
     * Accept Job
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        $response = $this->_bookingRepo->acceptJob($data, $user);
        return response($response);
    }

    /**
     * Accept New job
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;
        $response = $this->_bookingRepo->acceptJobWithId($data, $user);
        return response($response ?? []);
    }

    /**
     * Cancel job
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        $response = $this->_bookingRepo->cancelJobAjax($data, $user);
        return response($response ?? []);
    }

    /**
     * End Job
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();
        $response = $this->_bookingRepo->endJob($data);
        return response($response ?? []);
    }

    /**
     * Potential Jobs
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;
        $response = $this->_bookingRepo->getPotentialJobs($user);
        return response($response ?? []);
    }

    /**
     * Reopening
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->_bookingRepo->reopen($data);
        return response($response);
    }

    /**
     * Customer not call
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function customerNotCall(Request $request)
    {
        $data = $request->all();
        $response = $this->_bookingRepo->customerNotCall($data);
        return response($response ?? []);
    }

    /**
     * Resend Notification
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->_bookingRepo->find($data['jobid']);
        $job_data = $this->_bookingRepo->jobToData($job);
        $this->_bookingRepo->sendNotificationTranslator($job, $job_data, '*');
        return response(['success' => 'Push sent']);
    }

    /**
     * Resend SMS
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->_bookingRepo->find($data['jobid']);

        try {
            $this->_bookingRepo->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);

        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        $n_data = [
            'time'         => $this->_assignVal($data, 'time'),
            'jobid'        => $this->_assignVal($data, 'jobid'),
            'distance'     => $this->_assignVal($data, 'distance'),
            'session_time' => $this->_assignVal($data, 'session_time'),
            'admincomment' => $this->_assignVal($data, 'admincomment'),
        ];

        if ($n_data['time'] || $n_data['distance']) {

            $affectedRows = Distance::where('job_id', '=', $n_data['jobid'])
                ->update([
                    'distance' => $n_data['distance'],
                    'time' => $n_data['time']
                ]);
        }

        if ($n_data['admincomment'] || $n_data['session_time'] || $flagged || $manually_handled || $by_admin) {
            $affectedRows1 = Job::where('id', '=', $n_data['jobid'])
                ->update([
                    'admin_comments' => $n_data['admincomment'],
                    'flagged' => $flagged,
                    'session_time' => $n_data['session_time'],
                    'manually_handled' => $manually_handled,
                    'by_admin' => $by_admin
                ]);
        }

        return response('Record updated!');
    }

    /**
     * Assign Val
     *
     * @param $array
     * @param $key
     *
     * @return string
     */
    private function _assignVal($array, $key)
    {
        return isset($array[$key]) && $array[$key] != "" ? $array[$key] : "";
    }
}