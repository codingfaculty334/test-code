<?php

namespace DTApi\Http\Controllers;

use App\Contracts\IBookingServiceContract;
use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 *
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * BookingService Instance
     *
     * @var IBookingServiceContract
     */
    private $_bookingService;

    /**
     * BookingController constructor.
     *
     * @param IBookingServiceContract $_bookingService
     */
    public function __construct(IBookingServiceContract $_bookingService)
    {
        $this->_bookingService = $_bookingService;
    }

    /**
     * Show Booking Index
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        return $this->_bookingService->index($request);
    }

    /**
     * Show Single Booking
     *
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return $this->_bookingService->show($id);
    }

    /**
     * Store New Booking
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        return $this->_bookingService->store($request);
    }

    /**
     * Update Booking
     *
     * @param $id
     * @param Request $request
     *
     * @return mixed
     */
    public function update($id, Request $request)
    {
        return $this->_bookingService->update($request, $id);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        return $this->_bookingService->immediateJobEmail($request);
    }

    /**
     * Get History
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        return $this->_bookingService->getHistory($request);
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
        return $this->_bookingService->acceptJob($request);
    }

    /**
     * Accept job with id
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function acceptJobWithId(Request $request)
    {
        return $this->_bookingService->acceptJobWithId($request);
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
        return $this->_bookingService->cancelJob($request);
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
        return $this->_bookingService->endJob($request);
    }

    /**
     * Customer Not call
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function customerNotCall(Request $request)
    {
        return $this->_bookingService->customerNotCall($request);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        return $this->_bookingService->getPotentialJobs($request);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function distanceFeed(Request $request)
    {
        return $this->_bookingService->distanceFeed($request);
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
        return $this->_bookingService->reopen($request);
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
        return $this->_bookingService->resendNotifications($request);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function resendSMSNotifications(Request $request)
    {
        return $this->_bookingService->resendSMSNotifications($request);
    }

}
