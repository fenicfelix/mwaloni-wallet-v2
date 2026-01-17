<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;

class ProfileComponent extends Component
{
    use NotifyBrowser;

    public string $content_title = "My Profile";

    public bool $changePassword = false;

    public ?User $user = null;

    public ?string $current_password = null;

    public ?string $new_password = null;

    public ?string $confirm_password = null;

    public function mount()
    {
        $this->initializeValues();
    }

    public function resetVariables()
    {
        $this->reset('changePassword', 'current_password', 'new_password', 'confirm_password');
    }

    private function initializeValues()
    {
        $this->user = Auth::user();
        $this->resetVariables();
    }

    public function toggleView()
    {
        $this->changePassword = !$this->changePassword;
    }

    public function rules()
    {
        $rules =  [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:10',             // Must be at least 10 characters in length
                'regex:/[a-z]/',      // Must contain at least one lowercase letter
                'regex:/[A-Z]/',      // Must contain at least one uppercase letter
                'regex:/[0-9]/',      // Must contain at least one digit
                'regex:/[@$!%*#?&]/', // Must contain a special character
            ],
            'confirm_password' => 'required|same:new_password',
        ];

        return $rules;
    }

    public function changePassword()
    {
        $this->validate();

        if (!password_verify($this->current_password, $this->user->password)) {
            $this->notify("Invalid Password.", "error");
        } else {
            $this->user->password = Hash::make($this->new_password);
            if ($this->user->save()) $this->notify("Password has been changed.", "success");
            else $this->notify("Password not saved. Please try again later.", "error");

            $this->resetVariables();
        }
    }

    public function render()
    {
        return view('core::livewire.profile-component')
            ->layout('core::layouts.app');
    }
}
