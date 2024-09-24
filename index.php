<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    session_start(); 
}

function registrarUsuario($username, $password) {
    $archivo = 'users.txt'; // Nombre del archivo donde se almacenarán los usuarios
    $datos = "$username,$password\n"; // Crear la línea con usuario y contraseña
    file_put_contents($archivo, $datos, FILE_APPEND); // Guardar en el archivo
}
function comprobarLogin($username, $password) {
    $archivo = 'users.txt'; // Nombre del archivo que contiene los usuarios
    if (file_exists($archivo)) { // Comprobar si el archivo existe
        $datosAlmacenados = file($archivo, FILE_IGNORE_NEW_LINES | // Es un Flag que lee el contenido de users.txt y almacena cada línea en un array sin saltos de línea
        FILE_SKIP_EMPTY_LINES);// Es un Flag que indica que debe saltar las líneas vacías
        foreach ($datosAlmacenados as $linea) {
            list($usuarioAlmacenado, $contraseñaAlmacenada) = explode(',', $linea); // Dividir usuario y contraseña
            if ($usuarioAlmacenado == $username && $contraseñaAlmacenada == $password) {
                return true; // Inicio de sesión exitoso
            }
        }
    }
    return false; // Inicio de sesión fallido
}

// Comprobar si el usuario ya ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Manejar el registro de usuario
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['passwd'] ?? '';
        registrarUsuario($username, $password); // Llamar a la función de registro
        echo 'Usuario registrado con éxito. Ahora puedes iniciar sesión.<br>';
    }

    // Manejar el inicio de sesión
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['passwd'] ?? '';
        if (comprobarLogin($username, $password)) {
            $_SESSION['user_id'] = 1; // Guardar la sesión
            echo 'Login exitoso. Bienvenido, ' . htmlspecialchars($username) . '!'; // Mensaje de bienvenida
        } else {
            echo 'Nombre de usuario o contraseña incorrectos.<br>'; // Mensaje de error
        }
    }

    // Formulario de registro
    echo '<h2>Registro</h2>';
    echo '<form method="post">'
        . '<input name="username" type="text" placeholder="Usuario" required>'
        . '<input name="passwd" type="password" placeholder="Contraseña" required>'
        . '<input type="hidden" name="action" value="register">'
        . '<button type="submit">Registrar</button>'
        . '</form>';

    // Formulario de inicio de sesión
    echo '<h2>Iniciar sesión</h2>';
    echo '<form method="post">'
        . '<input name="username" type="text" placeholder="Usuario" required>'
        . '<input name="passwd" type="password" placeholder="Contraseña" required>'
        . '<input type="hidden" name="action" value="login">'
        . '<button type="submit">Iniciar sesión</button>'
        . '</form>';
}

// Mostrar contenido para usuarios logueados
if (isset($_SESSION['user_id'])) {
    echo '<div><img src="https://m.media-amazon.com/images/I/81xjuz-wGrL._AC_UF1000,1000_QL80_.jpg" width="300"/></div>';
    echo '<a href="?logout=1">Cerrar sesión</a>'; // Enlace para cerrar sesión
}
?>
