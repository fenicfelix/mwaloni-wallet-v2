<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Wallet\Core\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniAuth;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Repositories\UserRepository;

class UsersComponent extends Component
{
    use NotifyBrowser, MwaloniWallet, MwaloniAuth;

    public ?string $content_title;

    public ?bool $add = false;

    public ?int $formId = null;

    public ?array $formData = [];

    public ?Collection $roles = null;

    public ?User $user = null;

    public ?bool $displayApiDetails = null;

    public function mount()
    {
        $this->roles = Role::get();
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Users Manager";
        $this->resetValues();
    }

    public function rules()
    {
        $rules =  [
            'formData.first_name' => 'required',
            'formData.last_name' => 'required',
            'formData.phone_number' => 'required',
            'formData.email' => 'required|unique:users,email',
            'formData.role_id' => 'required|exists:roles,id',
        ];

        if ($this->formId) {
            $rules["formData.email"] = 'required|unique:users,email,' . $this->formId;
        }

        return $rules;
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (\Throwable $th) {
            $this->notify(
                'There were validation errors. Please check the form and try again.',
                'error'
            );
            return;
        }

        $user_id = Auth::id();
        $this->formData["updated_by"] = $user_id;
        $this->formData["updated_at"] = date("Y-m-d H:i:s");

        if ($this->formId === null) {
            $this->formData["created_by"] = $user_id;
            $this->formData["created_at"] = date("Y-m-d H:i:s");
        }

        $user = null;
        $password = null;
        if ($this->formId === null) {
            $password = $this->generateRandomString("password");
            $this->formData["password"] = Hash::make($password);
            $user = app(UserRepository::class)->create($this->formData);
        } else {
            $user = app(UserRepository::class)->update($this->formId, $this->formData);
        }

        if (!$user) {
            $this->notify("Operation failed", "error");
            return;
        }

        if ($this->formId === null) {
            try {
                $this->generateApiUSername($user);

                $phone_number = cleanPhoneNumber($user->phone_number);
                $message = getOption('SMS-WELCOME');
                $message = str_replace("{name}", $user->first_name, $message);
                $message = str_replace("{password}", $password, $message);

                $this->sendSMS($phone_number, $message);
            } catch (\Throwable $th) {
                //throw $th;
                $this->notify("Failed to send SMS. Please try again.", "error");
                return;
            }
        }

        $this->notify("Operation was successful", "success");
        $this->dispatch('refreshDatatable');
        $this->resetValues();
    }

    #[On("deleteAccountFunction")]
    public function deleteAccountFunction($id)
    {
        $this->formId = $id;
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to delete this user?',
            'warning',
            'Yes, Delete',
            'confirmedDeletePassword'
        );
    }

    #[On("confirmedDeletePassword")]
    public function confirmedDeletePassword()
    {
        $deleted = app(UserRepository::class)->delete($this->formId);
        if (! $deleted) {
            $this->notify("Failed to delete user account. Please try again.", "error");
        }

        $this->notify("User account has been deleted.", "success");
        $this->dispatch('refreshDatatable');
    }

    #[On("resetPasswordFunction")]
    public function resetPasswordFunction($id)
    {
        $this->formId = $id;
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to reset this user\'s password?',
            'warning',
            'Yes, Reset',
            'confirmedResetPassword'
        );
    }

    #[On("confirmedResetPassword")]
    public function confirmedResetPassword()
    {
        $password = $this->generateRandomString("password");
        $updatedUser = app(UserRepository::class)->resetPassword($this->formId, $password);
        if ($updatedUser) {
            try {
                $phone_number = cleanPhoneNumber($updatedUser->phone_number);
                $message = getOption("SMS-PASSWORD-RESET");
                $message = str_replace("{name}", $updatedUser->first_name, $message);
                $message = str_replace("{password}", $password, $message);

                $this->sendSMS($phone_number, $message);

                $this->notify("Password has been reset and sent to " . $updatedUser->first_name . ".", "success");
            } catch (\Throwable $th) {
                //throw $th;
                $this->notify("Failed to send SMS. Please try again.", "error");
            }
        } else {
            $this->notify("Failed to reset password. Please try again.", "error");
        }
    }

    public function addFunction()
    {
        $this->resetValues();
        $this->add = !$this->add;
    }

    #[On("showApiDetails")]
    public function showApiDetails($id)
    {
        $this->user = User::find($id);
        $this->formData['password'] = $this->generateRandomString("password");
        $this->user->update(['password' => Hash::make($this->formData['password'])]);
        $this->displayApiDetails = true;
    }

    #[On("editFunction")]
    public function editFunction($id)
    {
        $this->resetValues();
        $this->formId = $id;
        $this->add = !$this->add;
        $this->user = User::with('role')->where("id", $id)->first();
        $this->formData = $this->user->toArray();
    }

    public function backAction()
    {
        $this->resetValues();
    }

    public function resetValues()
    {
        $this->reset('add', 'formId', 'formData', 'displayApiDetails');
    }

    public function render()
    {
        return view('core::livewire.users-component')
            ->layout('core::layouts.app');
    }
}
