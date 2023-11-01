<?php declare(strict_types=1);

class Login
{
    private int $id;
    private string $email;
    private string $password;
    private string $role;

    public function __construct()
    {
        $this->id = 0;
        $this->email = "";
        $this->password = "";
        $this->role = "";
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
        );
    }

    public function validate(): array {
        $errors = [];

        //Check email
        if(empty($this->email)) {
            $errors[] = ['email'=>'Email is required'];
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['email'=>'Invalid email format'];
        }

        //Check password
        if(empty($this->password)) {
            $errors[] = ['password'=>'Password is required'];
        } elseif (strlen($this->password) < 8) {
            $errors[] = ['password'=>'The password must have at least 8 characters'];
        }

        return $errors;
    }

    public function __toString(): string
    {
        return "ID: $this->id, email: $this->email";
    }


}
