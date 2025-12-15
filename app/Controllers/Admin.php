<?php

namespace App\Controllers;

use App\Models\User_Model;
use App\Models\Message_Model;
use App\Models\Profile_Model;
use App\Models\Package_Model;
use App\Models\Services_Model;
use App\Models\Appointment_Model;
use App\Models\Billing_Model;
use App\Models\Billing_Items_Model;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

use Ramsey\Uuid\Uuid;

class Admin extends BaseController
{
    private function sendWelcomeMessages(int $userId): void
    {
        $Message_Model = new \App\Models\Message_Model();

        $messages = [
            [
                'user_id' => $userId,
                'subject' => '🎉 Welcome to Dental Care Plus!',
                'content' => 'Thank you for joining our community! We’re excited to help you maintain a healthy smile.',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📦 Explore Our Dental Packages',
                'content' => 'Check out our affordable and comprehensive dental packages. Click here to explore: <a href="' . base_url("services") . '">View Services</a>',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📝 Complete Your Profile',
                'content' => 'To get the best experience, please complete your profile. <a href="' . base_url("client/profile") . '">Update Profile</a>',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📅 Book an Appointment Anytime',
                'content' => 'Easily schedule a visit with us at your convenience. Visit the appointments section anytime!',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        foreach ($messages as $msg) {
            $Message_Model->insert($msg);
        }
    }

    private function sendMessageToClient($user_id, $subject, $content)
    {
        $Message_Model = new \App\Models\Message_Model();

        $messageData = [
            'user_id' => $user_id,
            'subject' => $subject,
            'content' => $content,
            'received_at' => date('Y-m-d H:i:s'),
            'is_read' => 0,
            'is_deleted' => 0,
        ];

        $Message_Model->insert($messageData);
    }

    private function uploadImage(string $fieldName, string $uploadPath): array
    {
        $imageFile = $this->request->getFile($fieldName);

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Validate mime type
            $validMime = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($imageFile->getMimeType(), $validMime)) {
                return [
                    'success' => false,
                    'error_type' => 'invalid_image',
                ];
            }

            // Generate random name and move
            $newName = $imageFile->getRandomName();
            $imageFile->move($uploadPath, $newName);

            return [
                'success' => true,
                'filename' => $newName,
            ];
        }

        // No file uploaded — not an error, just "skip"
        return [
            'success' => false,
            'error_type' => null,
        ];
    }

    public function index()
    {
        $session = session();

        // Session check
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);
            return redirect()->to(base_url());
        }

        $user = $session->get('user');

        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);
            return redirect()->to(base_url());
        }

        // Load Models
        $appointmentModel = new Appointment_Model();
        $userModel = new User_Model();
        $serviceModel = new Services_Model();

        // Counts for info boxes
        $totalAppointments = $appointmentModel->countAllResults();
        $totalClients = $userModel->where('user_type', 'user')->countAllResults();
        $totalServices = $serviceModel->countAllResults();

        $appointments = $appointmentModel
            ->select('appointments.*, users.name as client_name')
            ->join('users', 'users.id = appointments.client_id', 'left')
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'user' => $user,
            'current_page' => 'dashboard',
            'current_page_title' => 'Dashboard',
            'totalAppointments' => $totalAppointments,
            'totalClients' => $totalClients,
            'totalServices' => $totalServices,
            'appointments' => $appointments
        ];

        $header = view('admin/templates/header', $data);
        $body = view('admin/dashboard', $data);
        $footer = view('admin/templates/footer');

        return $header . $body . $footer;
    }

    public function admin()
    {
        $session = session();

        // Check if 'user' exists in session
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);

            return redirect()->to(base_url());
        }

        // Get user data from session
        $user = $session->get('user');

        // Check if user_type is 'admin'
        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);

            return redirect()->to(base_url());
        }

        // ✅ Redirect admin to /admin/dashboard
        return redirect()->to(base_url('admin/dashboard'));
    }

    public function update_profile()
    {
        $id = session()->get('user')['id'];
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $User_Model = new User_Model();
        $existingUser = $User_Model->where('email', $email)->where('id !=', $id)->first();

        $success = false;
        $error_type = null;

        if ($existingUser) {
            $error_type = 'email_exists';
        } else {
            $updateData = [
                'name' => $name,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            // ✅ Use the reusable upload function
            $uploadPath = FCPATH . 'public/dist/admin/img/uploads/';
            $uploadResult = $this->uploadImage('image', $uploadPath);

            if ($uploadResult['success']) {
                $updateData['image'] = $uploadResult['filename'];
            } elseif ($uploadResult['error_type'] === 'invalid_image') {
                return $this->response->setJSON([
                    'success' => false,
                    'error_type' => 'invalid_image',
                ]);
            }

            if ($User_Model->update($id, $updateData)) {
                $success = true;

                $updatedUser = $User_Model->find($id);
                session()->set('user', $updatedUser);

                session()->setFlashdata([
                    'title' => 'Success!',
                    'text' => 'Profile updated successfully!',
                    'icon' => 'success',
                ]);
            } else {
                $error_type = 'db_error';
            }
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function appointments()
    {
        $session = session();

        // Check if 'user' exists in session
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);

            return redirect()->to(base_url());
        }

        // Get user data from session
        $user = $session->get('user');

        // Check if user_type is 'admin'
        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);

            return redirect()->to(base_url());
        }

        $current_page = 'appointments';
        $current_page_title = 'Appointments';

        $Services_Model = new Services_Model();
        $Appointment_Model = new Appointment_Model();
        $User_Model = new User_Model();

        $data = [
            'user' => $user,
            'current_page' => $current_page,
            'current_page_title' => $current_page_title,
        ];

        // Group services by category
        $services = $Services_Model->orderBy('category', 'ASC')->orderBy('name', 'ASC')->findAll();
        $groupedServices = [];
        foreach ($services as $service) {
            $groupedServices[$service['category']][] = $service['name'];
        }
        $data['groupedServices'] = $groupedServices;

        $data['users'] = $User_Model
            ->select('users.*, profiles.phone, profiles.address, profiles.birthdate, profiles.gender')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->where('users.user_type', 'user')
            ->findAll();

        $data['appointments'] = $Appointment_Model
            ->select('appointments.*, users.name as client_name')
            ->join('users', 'users.id = appointments.client_id', 'left')
            ->orderBy('appointments.created_at', 'DESC')
            ->findAll();

        $header = view('admin/templates/header', $data);
        $body = view('admin/appointments');
        $footer = view('admin/templates/footer');

        // Load appointments view if user is admin
        return $header . $body . $footer;
    }

    public function clients()
    {
        $session = session();

        // Check if 'user' exists in session
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);

            return redirect()->to(base_url());
        }

        // Get user data from session
        $user = $session->get('user');

        // Check if user_type is 'admin'
        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);

            return redirect()->to(base_url());
        }

        $current_page = 'clients';
        $current_page_title = 'Clients';

        $data = [
            'user' => $user,
            'current_page' => $current_page,
            'current_page_title' => $current_page_title,
        ];

        $User_Model = new User_Model();

        $data['users'] = $User_Model
            ->select('users.*, profiles.phone, profiles.address, profiles.birthdate, profiles.gender')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->where('users.user_type', 'user')
            ->orderBy('users.id', 'DESC')
            ->findAll();

        $header = view('admin/templates/header', $data);
        $body = view('admin/clients');
        $footer = view('admin/templates/footer');

        // Load clients view if user is admin
        return $header . $body . $footer;
    }

    public function services()
    {
        $session = session();

        // Check if 'user' exists in session
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);
            return redirect()->to(base_url());
        }

        // Get user data from session
        $user = $session->get('user');

        // Check if user_type is 'admin'
        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);
            return redirect()->to(base_url());
        }

        // === Load services data ===
        $Services_Model = new \App\Models\Services_Model();
        $services = $Services_Model
            ->orderBy('id', 'DESC')
            ->findAll();

        // === Prepare data for view ===
        $data = [
            'user' => $user,
            'current_page' => 'services',
            'current_page_title' => 'Services',
            'services' => $services,
        ];

        // === Render views ===
        $header = view('admin/templates/header', $data);
        $body   = view('admin/services', $data); // Pass $data here
        $footer = view('admin/templates/footer');

        return $header . $body . $footer;
    }

    public function billing()
    {
        $session = session();

        // Check login
        if (!$session->has('user')) {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'You must log in first!',
            ]);
            return redirect()->to(base_url());
        }

        $user = $session->get('user');

        if (!isset($user['user_type']) || $user['user_type'] !== 'admin') {
            $session->setFlashdata([
                'type' => 'error',
                'message' => 'Access denied! Admins only.',
            ]);
            return redirect()->to(base_url());
        }

        // Load models
        $User_Model = new \App\Models\User_Model();
        $Profile_Model = new \App\Models\Profile_Model();
        $Services_Model = new \App\Models\Services_Model();
        $Billing_Model = new \App\Models\Billing_Model();
        $Billing_Items_Model = new \App\Models\Billing_Items_Model();

        // Fetch clients and services for dropdowns
        $clients = $User_Model
            ->select('users.id, users.name, profiles.phone')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->where('user_type', 'user')
            ->orderBy('users.name', 'ASC')
            ->findAll();

        $services = $Services_Model->orderBy('name', 'ASC')->findAll();

        // Fetch billings
        $billings = $Billing_Model
            ->select('billings.*, users.name as client_name, services.name as service_name')
            ->join('users', 'users.id = billings.client_id', 'left')
            ->join('services', 'services.id = billings.service_id', 'left')
            ->orderBy('billings.id', 'DESC')
            ->findAll();

        // Prepare billing data with items
        foreach ($billings as &$billing) {
            $items = $Billing_Items_Model->where('billing_id', $billing['id'])->findAll();
            $misc_total = 0;
            $misc_list = [];

            foreach ($items as $item) {
                $misc_total += $item['misc_amount'];
                $misc_list[] = $item['misc_name'] . ': ' . number_format($item['misc_amount'], 2);
            }

            $billing['total_amount_with_items'] = $billing['main_service_amount'] + $misc_total;
            $billing['items_description'] = count($misc_list) > 0 ? implode(', ', $misc_list) : '';
        }

        $data = [
            'user' => $user,
            'current_page' => 'billing',
            'current_page_title' => 'Billing & Payments',
            'clients' => $clients,
            'services' => $services,
            'billings' => $billings,
        ];

        $header = view('admin/templates/header', $data);
        $body = view('admin/billing', $data);
        $footer = view('admin/templates/footer');

        return $header . $body . $footer;
    }

    public function get_user_profile()
    {
        $user_id = $this->request->getPost('user_id');

        $Profile_Model = new Profile_Model();
        $profile = $Profile_Model->where('user_id', $user_id)->first();

        if ($profile) {
            return $this->response->setJSON([
                'status' => 'success',
                'phone' => $profile['phone'],
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Profile not found.',
            ]);
        }
    }

    public function add_appointment()
    {
        $service = $this->request->getPost('service');
        $client_id = $this->request->getPost('client_id');
        $phone = $this->request->getPost('phone');
        $appointment_date = $this->request->getPost('appointment_date');
        $appointment_time = $this->request->getPost('appointment_time');

        $Appointment_Model = new Appointment_Model();
        $Profile_Model = new Profile_Model();
        $Message_Model = new \App\Models\Message_Model(); // Load message model

        $success = false;
        $error_type = null;

        // Format date & time
        $formatted_date = date('m/d/Y', strtotime($appointment_date));
        $formatted_time = date('g:ia', strtotime($appointment_time));

        // Prepare appointment data
        $insertData = [
            'service' => $service,
            'client_id' => $client_id,
            'phone' => $phone,
            'appointment_date' => $formatted_date,
            'appointment_time' => $formatted_time,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Insert into appointments table
        if ($Appointment_Model->insert($insertData)) {
            $Profile_Model->where('user_id', $client_id)
                ->set(['phone' => $phone])
                ->update();

            // Send message
            $this->sendMessageToClient(
                $client_id,
                'Appointment Confirmation',
                "Your appointment for {$service} has been scheduled on {$formatted_date} at {$formatted_time}."
            );

            $success = true;

            session()->setFlashdata([
                'title' => 'Success!',
                'text' => 'Appointment added successfully and message sent to the client!',
                'icon' => 'success',
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function cancel_appointment()
    {
        $id = $this->request->getPost('id');

        $Appointment_Model = new Appointment_Model();
        $Message_Model = new \App\Models\Message_Model();

        $success = false;
        $error_type = null;

        // Get appointment info before deleting
        $appointment = $Appointment_Model->find($id);

        if ($appointment && $Appointment_Model->delete($id)) {
            // Send message
            $this->sendMessageToClient(
                $appointment['client_id'],
                'Appointment Cancelled',
                "Your appointment for {$appointment['service']} on {$appointment['appointment_date']} at {$appointment['appointment_time']} has been cancelled."
            );

            $success = true;

            session()->setFlashdata([
                'title' => 'Success!',
                'text' => 'Appointment cancelled successfully and message sent to the client!',
                'icon' => 'success',
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function update_appointment()
    {
        $id = $this->request->getPost('id');
        $service = $this->request->getPost('service');
        $client_id = $this->request->getPost('client_id');
        $phone = $this->request->getPost('phone');
        $appointment_date = $this->request->getPost('appointment_date');
        $appointment_time = $this->request->getPost('appointment_time');

        $Appointment_Model = new Appointment_Model();
        $Profile_Model = new Profile_Model();
        $Message_Model = new \App\Models\Message_Model();

        $success = false;
        $error_type = null;

        $formatted_date = date('m/d/Y', strtotime($appointment_date));
        $formatted_time = date('g:ia', strtotime($appointment_time));

        $updateData = [
            'service' => $service,
            'client_id' => $client_id,
            'phone' => $phone,
            'appointment_date' => $formatted_date,
            'appointment_time' => $formatted_time,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'missing_id',
            ]);
        }

        if ($Appointment_Model->update($id, $updateData)) {
            $Profile_Model->where('user_id', $client_id)
                ->set(['phone' => $phone])
                ->update();

            // Send message
            $this->sendMessageToClient(
                $client_id,
                'Appointment Updated',
                "Your appointment for {$service} has been updated to {$formatted_date} at {$formatted_time}."
            );

            $success = true;

            session()->setFlashdata([
                'title' => 'Success!',
                'text' => 'Appointment updated successfully and message sent to the client!',
                'icon' => 'success',
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function get_appointment()
    {
        $id = $this->request->getPost('id');

        $Appointment_Model = new Appointment_Model();
        $appointment = $Appointment_Model->find($id);

        if ($appointment) {
            return $this->response->setJSON([
                'success' => true,
                'appointment' => $appointment,
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Appointment not found.',
            ]);
        }
    }

    public function add_client()
    {
        $User_Model = new User_Model();
        $Profile_Model = new Profile_Model();

        $success = false;
        $error_type = null;

        // === Gather POST data ===
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $phone = $this->request->getPost('phone');
        $birthdate = $this->request->getPost('birthdate');
        $gender = $this->request->getPost('gender');
        $address = $this->request->getPost('address');

        // === Check if email already exists ===
        $existingUser = $User_Model->where('email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'email_exists',
            ]);
        }

        // === Handle image upload ===
        $uploadResult = $this->uploadImage('image', FCPATH . 'public/dist/admin/img/uploads/');

        if ($uploadResult['success']) {
            $imageName = $uploadResult['filename'];
        } else {
            $imageName = 'default-user-image.webp';

            // Invalid image type → reject request
            if ($uploadResult['error_type'] === 'invalid_image') {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'invalid_image',
                ]);
            }
        }

        // === Prepare user data ===
        $userData = [
            'uuid'       => Uuid::uuid4()->toString(), // optional unique UUID
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'image'      => $imageName,
            'user_type'  => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // === Insert user ===
        if ($User_Model->insert($userData)) {
            $user_id = $User_Model->getInsertID();

            // Insert profile
            $Profile_Model->insert([
                'user_id'    => $user_id,
                'phone'      => $phone,
                'birthdate'  => $birthdate,
                'gender'     => $gender,
                'address'    => $address,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // ✅ Send welcome messages
            $this->sendWelcomeMessages($user_id);

            $success = true;

            session()->setFlashdata([
                'title' => 'Success!',
                'text'  => 'Client added successfully and welcome messages sent!',
                'icon'  => 'success',
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function delete_client()
    {
        $id = $this->request->getPost('id');

        $User_Model = new User_Model();
        $Profile_Model = new Profile_Model();

        $success = false;
        $error_type = null;

        // === Fetch the user first to get the image name ===
        $user = $User_Model->find($id);

        if ($user) {
            // === Delete user record ===
            if ($User_Model->delete($id)) {
                // Delete associated profile
                $Profile_Model->where('user_id', $id)->delete();

                // Delete uploaded image (except default)
                if (!empty($user['image']) && $user['image'] !== 'default-user-image.webp') {
                    $imagePath = FCPATH . 'public/dist/admin/img/uploads/' . $user['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $success = true;

                session()->setFlashdata([
                    'title' => 'Deleted!',
                    'text'  => 'Client deleted successfully!',
                    'icon'  => 'success',
                ]);
            } else {
                $error_type = 'db_error';
            }
        } else {
            $error_type = 'not_found';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function update_client()
    {
        $User_Model = new User_Model();
        $Profile_Model = new Profile_Model();

        $success = false;
        $error_type = null;

        // === Gather POST data ===
        $id        = $this->request->getPost('id');
        $name      = $this->request->getPost('name');
        $email     = $this->request->getPost('email');
        $password  = $this->request->getPost('password');
        $phone     = $this->request->getPost('phone');
        $birthdate = $this->request->getPost('birthdate');
        $gender    = $this->request->getPost('gender');
        $address   = $this->request->getPost('address');

        // === Fetch existing user ===
        $user = $User_Model->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'user_not_found',
            ]);
        }

        // === Check if new email is already taken by another user ===
        $existingUser = $User_Model
            ->where('email', $email)
            ->where('id !=', $id)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'email_exists',
            ]);
        }

        // === Handle image upload ===
        $uploadResult = $this->uploadImage('image', FCPATH . 'public/dist/admin/img/uploads/');
        $imageName = $user['image']; // keep old image by default

        if ($uploadResult['success']) {
            $imageName = $uploadResult['filename'];
        } elseif ($uploadResult['error_type'] === 'invalid_image') {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'invalid_image',
            ]);
        }

        // === Prepare user update data ===
        $userData = [
            'name'       => $name,
            'email'      => $email,
            'image'      => $imageName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Only update password if changed
        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // === Update user ===
        if ($User_Model->update($id, $userData)) {
            // Update or insert profile
            $existingProfile = $Profile_Model->where('user_id', $id)->first();
            $profileData = [
                'phone'      => $phone,
                'birthdate'  => $birthdate,
                'gender'     => $gender,
                'address'    => $address,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existingProfile) {
                $Profile_Model->where('user_id', $id)->set($profileData)->update();
            } else {
                $profileData['user_id'] = $id;
                $profileData['created_at'] = date('Y-m-d H:i:s');
                $Profile_Model->insert($profileData);
            }

            // ✅ Send message to client about update
            $this->sendMessageToClient(
                $id,
                'Profile Updated',
                "Hello {$name}, your profile has been successfully updated."
            );

            $success = true;

            session()->setFlashdata([
                'title' => 'Success!',
                'text'  => 'Client updated successfully and message sent!',
                'icon'  => 'success',
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function get_client()
    {
        $id = $this->request->getPost('id');

        $User_Model = new \App\Models\User_Model();

        $success = false;
        $client = null;

        $user = $User_Model
            ->select('users.*, profiles.phone, profiles.address, profiles.birthdate, profiles.gender')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->where('users.id', $id)
            ->first();

        if ($user) {
            $success = true;
            $client = $user;
        }

        return $this->response->setJSON([
            'success' => $success,
            'client' => $client,
        ]);
    }

    public function add_service()
    {
        $Services_Model = new Services_Model();

        $name = $this->request->getPost('name');
        $category = $this->request->getPost('category');

        $success = false;
        $error_type = null;

        if (empty($name) || empty($category)) {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'validation_error'
            ]);
        }

        if ($Services_Model->insert([
            'name' => $name,
            'category' => $category
        ])) {
            $success = true;
            session()->setFlashdata([
                'title' => 'Success!',
                'text'  => 'Service added successfully!',
                'icon'  => 'success'
            ]);
        } else {
            $error_type = 'db_error';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type
        ]);
    }

    public function delete_service()
    {
        $Services_Model = new Services_Model();
        $success = false;
        $error_type = null;

        $id = $this->request->getPost('id');

        if ($id) {
            $service = $Services_Model->find($id);

            if ($service) {
                if ($Services_Model->delete($id)) {
                    $success = true;

                    session()->setFlashdata([
                        'title' => 'Deleted!',
                        'text'  => 'Service deleted successfully!',
                        'icon'  => 'success',
                    ]);
                } else {
                    $error_type = 'db_error';
                }
            } else {
                $error_type = 'not_found';
            }
        } else {
            $error_type = 'missing_id';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function get_service()
    {
        $Services_Model = new Services_Model();

        $id = $this->request->getPost('id');
        $service = $Services_Model->find($id);

        if ($service) {
            return $this->response->setJSON([
                'success' => true,
                'service' => $service
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
        ]);
    }

    public function update_service()
    {
        $Services_Model = new Services_Model();

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $category = $this->request->getPost('category');

        $success = false;
        $error_type = null;

        if ($id && $name && $category) {
            $existing = $Services_Model->find($id);

            if ($existing) {
                $updateData = [
                    'name' => $name,
                    'category' => $category,
                ];

                if ($Services_Model->update($id, $updateData)) {
                    $success = true;

                    session()->setFlashdata([
                        'title' => 'Updated!',
                        'text' => 'Service updated successfully!',
                        'icon' => 'success',
                    ]);
                } else {
                    $error_type = 'db_error';
                }
            } else {
                $error_type = 'not_found';
            }
        } else {
            $error_type = 'missing_fields';
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function add_billing()
    {
        $Billing_Model = new \App\Models\Billing_Model();
        $Billing_Items_Model = new \App\Models\Billing_Items_Model();

        $client_id = $this->request->getPost('client_id');
        $service_id = $this->request->getPost('service_id');
        $main_service_amount = (float)$this->request->getPost('main_service_amount');
        $total_amount = (float)$this->request->getPost('total_amount');
        $payment_date = $this->request->getPost('payment_date');
        $misc_services = $this->request->getPost('misc_service');
        $misc_amounts = $this->request->getPost('misc_amount');

        if (!$client_id || !$service_id || !$main_service_amount || !$total_amount || !$payment_date) {
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'missing_fields',
            ]);
        }

        $billingData = [
            'client_id' => $client_id,
            'service_id' => $service_id,
            'main_service_amount' => $main_service_amount,
            'total_amount' => $total_amount,
            'payment_date' => $payment_date,
        ];

        if ($Billing_Model->insert($billingData)) {
            $billing_id = $Billing_Model->getInsertID();

            // Add misc items
            if (!empty($misc_services) && is_array($misc_services)) {
                foreach ($misc_services as $index => $desc) {
                    $desc = trim($desc);
                    $amt = isset($misc_amounts[$index]) ? (float)$misc_amounts[$index] : 0;

                    if ($desc !== '' && $amt > 0) {
                        $Billing_Items_Model->insert([
                            'billing_id' => $billing_id,
                            'misc_name' => $desc,
                            'misc_amount' => $amt,
                        ]);
                    }
                }
            }

            // ✅ Send message to client with receipt link
            $receiptLink = base_url("/print_billing/{$billing_id}");
            $this->sendMessageToClient(
                $client_id,
                'Payment Recorded',
                "Hello! Your payment of ₱" . number_format($total_amount, 2) . " has been successfully recorded. " .
                    "You can print your receipt here: <a href='{$receiptLink}' target='_blank'>Print Receipt</a>"
            );

            session()->setFlashdata([
                'title' => 'Success!',
                'text' => 'Payment recorded successfully and message sent to the client!',
                'icon' => 'success',
            ]);

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error_type' => 'db_error',
            'error_message' => $Billing_Model->errors(),
        ]);
    }

    public function print_billing($id)
    {
        $Billing_Model = new \App\Models\Billing_Model();
        $Billing_Items_Model = new \App\Models\Billing_Items_Model();

        // Fetch billing record
        $billing = $Billing_Model
            ->select('billings.*, users.name as client_name, users.email, profiles.phone, services.name as service_name')
            ->join('users', 'users.id = billings.client_id', 'left')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->join('services', 'services.id = billings.service_id', 'left')
            ->where('billings.id', $id)
            ->first();

        if (!$billing) {
            return redirect()->back()->with('error', 'Billing record not found.');
        }

        // Fetch billing items
        $items = $Billing_Items_Model->where('billing_id', $billing['id'])->findAll();
        $misc_total = array_sum(array_column($items, 'misc_amount'));

        $billing['items'] = $items;
        $billing['total_amount_with_items'] = $billing['main_service_amount'] + $misc_total;

        // Generate QR code
        $builder = new Builder(
            writer: new PngWriter(),
            data: base_url("/admin/verify_receipt/" . $billing['id']),
            encoding: new Encoding('UTF-8'),
            size: 150,
            margin: 5
        );

        $qrCode = $builder->build();
        $qrCodeDataUri = $qrCode->getDataUri();

        return view('admin/billing_print', [
            'billing' => $billing,
            'qrCodeDataUri' => $qrCodeDataUri
        ]);
    }
}
