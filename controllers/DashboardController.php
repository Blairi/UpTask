<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController{
    public static function index(Router $router)
    {

        session_start();

        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }


    public static function crear_proyecto(Router $router)
    {

        session_start();
        
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);
            
            // Validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                // Generar una URL única

                # TODO: Crear metodo en modelo
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }


    public static function proyecto(Router $router)
    {
        session_start();

        isAuth();

        $token = $_GET['id'];

        if(!$token) header('Location: /dashboard');

        // Revisar que la persona que visita el proyecto sea el que la creo
        $proyecto = Proyecto::where('url', $token);
        
        if( $proyecto->propietarioId !== $_SESSION['id'] ){
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }


    public static function perfil(Router $router)
    {
        session_start();

        isAuth();

        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){


                $existeUsuario = Usuario::where('email', $usuario->email);
                
                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Mensaje error
                    Usuario::setAlerta('error', 'Email no válido, ya pertenece a otra cuenta');
                    $alertas = $usuario->getAlertas();

                }
                else{
                    // Guardar usuario
                    $usuario->guardar();
    
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
    
                    // Actualizar sesión
                    $_SESSION['nombre'] = $usuario->nombre;
                }


            }

        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }


    public static function cambiar_password(Router $router)
    {

        session_start();

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario = Usuario::find($_SESSION['id']);

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();
                
                if($resultado){

                    $usuario->password = $usuario->password_nuevo;
                    
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear password
                    $usuario->hashPassword();

                    // Actualizar
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Passoword guardado correctamente');
                        $alertas = $usuario->getAlertas();    
                    }

                }
                else{
                    Usuario::setAlerta('error', 'Passoword incorrecto');
                    $alertas = $usuario->getAlertas();
                }

            }
        }

        $router->render('dashboard/cambiar-password',[
            'alertas' => $alertas,
            'titulo' => 'Cambiar Password',
        ]);
    }

}