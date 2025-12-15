<?php

namespace App\Controllers;

use App\Models\Appointment_Model;
use App\Models\Profile_Model;
use App\Models\User_Model;
use App\Models\Message_Model;

class Client extends BaseController
{
    private function redirectIfNotClient()
    {
        $user = session()->get('user');
        if (!$user) {
            session()->setFlashdata(['type' => 'error', 'message' => 'You must be logged in to access this page.']);
            return redirect()->to(base_url('/'));
        }
        if ($user['user_type'] !== 'user') {
            session()->setFlashdata(['type' => 'error', 'message' => 'You do not have permission to access this page.']);
            return redirect()->to(base_url('admin/dashboard'));
        }
        return null;
    }

    private function getUnreadCount(): int
    {
        if (!session()->has('user') || session('user')['user_type'] !== 'user') {
            return 0;
        }
        return (new Message_Model())
            ->where('user_id', session('user')['id'])
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function dashboard()
    {
        if ($redirect = $this->redirectIfNotClient()) return $redirect;

        session()->set('current_page', 'dashboard');
        session()->set('page_title', 'Dashboard');
        session()->set('page_description', 'Account Analytics Overview');

        $userId = session('user')['id'];

        $Appointment_Model = new Appointment_Model();
        $Message_Model     = new Message_Model();

        $appointments = $Appointment_Model
            ->where('client_id', $userId)
            ->orderBy('appointment_date', 'DESC')
            ->findAll();

        $appointmentCount = count($appointments);

        $messageCount = $Message_Model
            ->where('user_id', $userId)
            ->where('is_deleted !=', 1)
            ->countAllResults();

        return view("landing/layouts/header", ['unreadCount' => $this->getUnreadCount()])
            . view("client/dashboard", [
                'appointments'     => $appointments,
                'appointmentCount' => $appointmentCount,
                'messageCount'     => $messageCount,
            ])
            . view("landing/layouts/footer");
    }

    public function profile()
    {
        if ($redirect = $this->redirectIfNotClient()) return $redirect;
        session()->set('current_page', 'profile');
        session()->set('page_title', 'Profile');
        session()->set('page_description', 'Personal Details');

        $user       = session('user');
        $profile    = (new Profile_Model())->where('user_id', $user['id'])->first();
        $userData   = (new User_Model())->find($user['id']);

        return view("landing/layouts/header", ['unreadCount' => $this->getUnreadCount()])
            . view("client/profile", [
                'profile' => $profile,
                'user'    => $userData,
            ])
            . view("landing/layouts/footer");
    }

    public function appointments()
    {
        if ($redirect = $this->redirectIfNotClient()) return $redirect;
        session()->set('current_page', 'appointments');
        session()->set('page_title', 'Appointments');
        session()->set('page_description', 'Appointment History & Status');

        $appointments = (new Appointment_Model())
            ->where('client_id', session('user')['id'])
            ->orderBy('appointment_date', 'DESC')
            ->findAll();

        return view("landing/layouts/header", ['unreadCount' => $this->getUnreadCount()])
            . view("client/appointments", ['appointments' => $appointments])
            . view("landing/layouts/footer");
    }

    public function messages()
    {
        if ($redirect = $this->redirectIfNotClient()) return $redirect;

        session()->set('current_page', 'messages');
        session()->set('page_title', 'Messages');
        session()->set('page_description', 'Messages Inbox');

        $messages = (new Message_Model())
            ->where('user_id', session('user')['id'])
            ->where('is_deleted', false)
            ->orderBy('received_at', 'DESC')
            ->findAll();

        $unreadCount = 0;
        $readCount   = 0;

        foreach ($messages as &$msg) {
            $msg['unread'] = !$msg['is_read'];
            $msg['received_at'] = date('Y-m-d H:i:s', strtotime($msg['received_at']));
            // ❌ Don't strip tags here — let the view handle safe rendering
            if ($msg['unread']) $unreadCount++;
            else $readCount++;
        }

        return view("landing/layouts/header", ['unreadCount' => $unreadCount])
            . view("client/messages", compact('messages', 'unreadCount', 'readCount'))
            . view("landing/layouts/footer");
    }

    public function markMessageRead()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $id     = $this->request->getPost('id');
        $userId = session('user')['id'];
        $model  = new Message_Model();

        if ($msg = $model->where('id', $id)->where('user_id', $userId)->first()) {
            if (!$msg['is_read']) {
                $model->update($id, ['is_read' => 1]);
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function update_profile()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request type.',
            ]);
        }

        $user = session()->get('user');

        if (!$user || $user['user_type'] !== 'user') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.',
            ]);
        }

        $userId = $user['id'];

        $User_Model = new User_Model();
        $Profile_Model = new Profile_Model();

        // Get posted data
        $name      = trim($this->request->getPost('name'));
        $email     = trim($this->request->getPost('email'));
        $phone     = trim($this->request->getPost('phone'));
        $address   = trim($this->request->getPost('address'));
        $birthdate = trim($this->request->getPost('birthdate'));
        $gender    = trim($this->request->getPost('gender'));

        // Email validation
        $emailExists = $User_Model
            ->where('email', $email)
            ->where('id !=', $userId)
            ->first();

        if ($emailExists) {
            return $this->response->setJSON([
                'success' => false,
                'field' => 'email',
                'message' => 'Email is already in use by another account.',
            ]);
        }

        // ✅ Handle image upload
        $imageName = $user['image'] ?? null;
        $imageFile = $this->request->getFile('image');

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = uniqid('client_') . '.' . $imageFile->getExtension();
            $uploadPath = FCPATH . 'public/uploads/client';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $imageFile->move($uploadPath, $newName);

            // Delete old image if not default
            if (!empty($imageName) && $imageName !== 'default-user-image.webp') {
                $oldPath = $uploadPath . '/' . $imageName;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageName = $newName;
        }

        // ✅ Update `users` table
        $User_Model->update($userId, [
            'name'  => $name,
            'email' => $email,
            'image' => $imageName,
        ]);

        // ✅ Update or Insert `profiles` table
        $existingProfile = $Profile_Model->where('user_id', $userId)->first();

        $profileData = [
            'user_id'   => $userId,
            'phone'     => $phone,
            'address'   => $address,
            'birthdate' => $birthdate,
            'gender'    => $gender,
        ];

        if ($existingProfile) {
            $Profile_Model->update($existingProfile['id'], $profileData);
        } else {
            $Profile_Model->insert($profileData);
        }

        // ✅ Update session name + email + image
        $updatedUser = $User_Model->find($userId);
        session()->set('user', $updatedUser);

        session()->setFlashdata([
            'type' => 'success',
            'message' => 'Your profile has been updated successfully.',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function change_password()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request type.',
            ]);
        }

        $user = session()->get('user');

        if (!$user || $user['user_type'] !== 'user') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.',
            ]);
        }

        $User_Model = new \App\Models\User_Model();
        $userData = $User_Model->find($user['id']);

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validate current password
        if (!password_verify($currentPassword, $userData['password'])) {
            return $this->response->setJSON([
                'success' => false,
                'field' => 'current_password',
                'message' => 'Current password is incorrect.',
            ]);
        }

        // Validate new password format
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'field' => 'new_password',
                'message' => 'Password must be at least 8 characters, include letters, numbers, and symbols.',
            ]);
        }

        // Match confirm password
        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'field' => 'confirm_password',
                'message' => 'New password and confirm password do not match.',
            ]);
        }

        // Save hashed password
        $User_Model->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        session()->setFlashdata([
            'type' => 'success',
            'message' => 'Your password has been changed successfully.',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    public function add_appointment()
    {
        $Appointment_Model = new Appointment_Model();
        $Profile_Model = new Profile_Model(); // <-- Add this line

        $client_id = $this->request->getPost("client_id");
        $phone = $this->request->getPost("phone");

        $data = [
            'client_id' => $client_id,
            'service' => $this->request->getPost("service"),
            'appointment_date' => $this->request->getPost("appointment_date"),
            'appointment_time' => $this->request->getPost("appointment_time"),
            'phone' => $phone,
        ];

        $success = false;
        $error_type = null;

        if ($Appointment_Model->insert($data)) {
            // ✅ Appointment added successfully
            $success = true;

            // ✅ Update profile phone only if a new phone number is provided
            if (!empty($phone)) {
                $Profile_Model
                    ->where('user_id', $client_id)
                    ->set(['phone' => $phone])
                    ->update();
            }

            session()->setFlashdata([
                "type" => "success",
                "message" => "Appointment created successfully!",
            ]);
        } else {
            $success = false;
            $error_type = "db_error";
        }

        return $this->response->setJSON([
            "success" => $success,
            "error_type" => $error_type
        ]);
    }

    public function delete_appointment($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request.',
            ]);
        }

        $user = session()->get('user');
        if (!$user || $user['user_type'] !== 'user') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
        }

        $Appointment_Model = new Appointment_Model();
        $appointment = $Appointment_Model->find($id);

        if (!$appointment || $appointment['client_id'] != $user['id']) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Appointment not found.',
            ]);
        }

        // Check if appointment is still upcoming
        $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
        if ($appointmentDateTime <= time()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only upcoming appointments can be canceled.',
            ]);
        }

        $Appointment_Model->delete($id);

        // 🔔 Set flashdata for success
        session()->setFlashdata([
            'type'    => 'success',
            'message' => 'Appointment canceled successfully.',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Appointment canceled successfully.',
        ]);
    }

    public function delete_message($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $user = session()->get('user');

        if (!$user || $user['user_type'] !== 'user') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized.']);
        }

        $Message_Model = new \App\Models\Message_Model();

        $message = $Message_Model
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$message) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Message not found.']);
        }

        // Perform soft delete
        $Message_Model->update($id, ['is_deleted' => 1]);

        session()->setFlashdata([
            'type' => 'success',
            'message' => 'Message deleted successfully.',
        ]);

        return $this->response->setJSON(['success' => true]);
    }
}
