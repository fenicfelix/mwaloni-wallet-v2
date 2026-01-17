<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Wallet\Core\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniAuth;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\NotifyBrowser;

class UsersComponent extends Component
{
    use NotifyBrowser, MwaloniWallet, MwaloniAuth;

    public ?string $content_title;

    public ?bool $add = false;

    public ?int $editId = null;

    public ?array $form = [];

    public ?Collection $roles = null;

    public ?User $user = null;

    public ?bool $displayApiDetails = null;

    public ?array $selectedRoles = null;

    public $listeners = [
        'showApiDetails',
        'editFunction',
        'resetPasswordFunction'
    ];

    public function mount()
    {
        $this->roles = Role::get();
        $this->initializeValues();
    }

    public function resetView()
    {
        $this->reset('add', 'editId', 'form', 'selectedRoles', 'displayApiDetails');
    }

    private function initializeValues()
    {
        $this->content_title = "Users Manager";
        $this->resetView();
    }

    public function addFunction()
    {
        $this->resetView();
        $this->add = !$this->add;
    }

    public function backToList()
    {
        $this->resetView();
    }

    public function showApiDetails($id)
    {
        $this->user = User::find($id);
        $this->form['password'] = $this->generateRandomString("password");
        $this->user->update(['password' => Hash::make($this->form['password'])]);
        $this->displayApiDetails = true;
    }

    public function editFunction($id)
    {
        $this->resetView();
        $this->editId = $id;
        $this->add = !$this->add;
        $this->user = User::with('roles')->where("id", $id)->first();
        $this->form = $this->user->toArray();
        $this->selectedRoles = $this->user->roles->pluck("id");
    }

    public function reset2FAFunction($id)
    {
        $this->editId = $id;
    }

    public function resetPasswordFunction($id)
    {
        $user = User::where("id", "=", $id)->first();
        if ($user) {
            $password = $this->generateRandomString("password");
            $user->password = Hash::make($password);
            if ($user->save()) {
                $phone_number = clean_phone_number($user->phone_number);
                $message = get_option("SMS-PASSWORD-RESET");
                $message = str_replace("{name}", $user->first_name, $message);
                $message = str_replace("{password}", $password, $message);

                $this->send_sms($phone_number, $message);

                $this->notify("Password has been reset and sent to " . $user->first_name . ".", "success");
            } else {
                $this->notify("Unable to reset the password. Please try again later", "error");
            }
        } else {
            $this->notify("The user was not found.", "error");
        }
    }

    public function rules()
    {
        $rules =  [
            'form.first_name' => 'required',
            'form.last_name' => 'required',
            'form.phone_number' => 'required',
            'form.email' => 'required|unique:users,email',
            'selectedRoles' => 'required',
        ];

        if ($this->editId) {
            $rules["form.email"] = 'required|unique:users,email,' . $this->editId;
        }

        return $rules;
    }

    public function store()
    {
        $this->validate();

        $status = DB::transaction(
            function () {
                $user_id = Auth::id();
                $this->form["updated_by"] = $user_id;
                $this->form["updated_at"] = date("Y-m-d H:i:s");

                if ($this->editId) {
                    $user = User::where("id", $this->editId)->first();
                    if (DB::table('sp_model_has_roles')->where('model_id', $user->id)->delete());
                    if ($user->syncRoles($this->selectedRoles));
                } else {
                    $password = $this->generateRandomString("password");
                    $this->form["identifier"] = generate_identifier();
                    $this->form["username"] = explode("@", $this->form["email"])[0];
                    $this->form["password"] = Hash::make($password);
                    $this->form["api_key"] = $this->generateApiKey(32);
                    $this->form["active"] = 1;
                }

                // insert or update the user
                $user = User::query()->updateOrCreate(
                    ['id' => $this->editId],
                    $this->form
                );

                if (!$this->editId) {
                    $user->assignRole($this->selectedRoles);
                    $this->generateApiUSername($user);

                    $phone_number = clean_phone_number($user->phone_number);
                    $message = get_option('SMS-WELCOME');
                    $message = str_replace("{name}", $user->first_name, $message);
                    $message = str_replace("{password}", $password, $message);

                    $this->send_sms($phone_number, $message);
                }

                return true;
            }
        );
        if ($status) {
            if ($this->editId) $this->notify("The user has been updated.", "success");
            else $this->notify("The user has been added.", "success");
            $this->resetView();
        } else {
            if ($this->editId) $this->notify("The user was not updated. Please try again.", "error");
            else $this->notify("The user could not be added. Please try again.", "error");
        }
    }

    public function render()
    {
        return view('core::livewire.users-component')
            ->layout('core::layouts.app');
    }
}
