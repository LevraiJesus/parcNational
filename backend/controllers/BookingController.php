<?php
require_once '../models/Booking.php';

class BookingController {
    private $booking;

    public function __construct($db) {
        $this->booking = new Booking($db);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        
        $this->booking->camping_id = $data->camping_id;
        $this->booking->user_id = $data->user_id;
        $this->booking->start_date = $data->start_date;
        $this->booking->end_date = $data->end_date;
        $this->booking->status = 'pending';

        if($this->booking->checkAvailability($data->camping_id, $data->start_date, $data->end_date)) {
            if($this->booking->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Booking was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create booking."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Camping is not available for the selected dates."));
        }
    }

    public function read($id) {
        $booking = $this->booking->read($id);
        if ($booking) {
            echo json_encode($booking);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Booking not found."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        
        $this->booking->id = $id;
        $this->booking->camping_id = $data->camping_id;
        $this->booking->user_id = $data->user_id;
        $this->booking->start_date = $data->start_date;
        $this->booking->end_date = $data->end_date;
        $this->booking->status = $data->status;

        if($this->booking->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Booking was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update booking."));
        }
    }

    public function delete($id) {
        if($this->booking->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "Booking was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete booking."));
        }
    }

    public function index() {
        $bookings = $this->booking->getAllBookings();
        echo json_encode($bookings);
    }

    public function checkAvailability() {
        $data = json_decode(file_get_contents("php://input"));
        
        $isAvailable = $this->booking->checkAvailability($data->camping_id, $data->start_date, $data->end_date);
        
        if($isAvailable) {
            echo json_encode(array("available" => true));
        } else {
            echo json_encode(array("available" => false));
        }
    }
}
