<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? '';
    }


    public function validarLogin()
    {
        if(!$this->email){
            self::$alertas['error'][] = 'El Email de Usuario es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no Válido';
        }
        if( strlen($this->email) > 30){
            self::$alertas['error'][] = 'El Email no Puede Tener más de 30 Caracteres';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El Password de Usuario es Obligatorio';
        }
        if( strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe contener al menos 6 Caracteres';
        }

        return self::$alertas;
    }


    public function validarNuevaCuenta()
    {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre de Usuario es Obligatorio';
        }
        if( strlen($this->nombre) > 30){
            self::$alertas['error'][] = 'El Nombre no Puede Tener más de 30 Caracteres';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El Email de Usuario es Obligatorio';
        }
        if( strlen($this->email) > 30){
            self::$alertas['error'][] = 'El Email no Puede Tener más de 30 Caracteres';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El Password de Usuario es Obligatorio';
        }
        if( strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe contener al menos 6 Caracteres';
        }

        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Los Password No Coinciden';
        }

        return self::$alertas;
    }


    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no Válido';
        }

        return self::$alertas;
    }


    public function validarPassword()
    {
        if(!$this->password){
            self::$alertas['error'][] = 'El Password de Usuario es Obligatorio';
        }
        if( strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe contener al menos 6 Caracteres';
        }
        return self::$alertas;
    }


    public function validar_perfil()
    {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        return self::$alertas;
    }

    public function nuevoPassword() : array {
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El Passoword Actual no puede ir vacio';
        }

        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El Passoword Nuevo no puede ir vacio';
        }

        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El Passoword debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }


    public function comprobar_password() : bool{
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar token
    public function crearToken() : void
    {
        $this->token = uniqid();
    }
}