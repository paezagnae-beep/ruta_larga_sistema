
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>proyecto ruta larga</title>
    <link rel="stylesheet" href="../CSS/registro.css">
</head>
<body>
    <header>
        <h2 class="logo">LOGO</h2>
    </header> 
    <div class="wrapper">
        <div class="form-boxlogin">
            <h2 class="title">Iniciar sesion</h2>
            <form action="menu.php" method="post">
                <div class="input-box">
                    <span class="icono"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="" id="" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icono"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="" id="">
                    <label>Contraseña</label>
                </div>
                <div class="recordar-olvidar">
                    <label><input type="checkbox" name="" id="">Recordar contraseña</label><br>
                    <a href=""> Olvidar contraseña</a>
                </div>
                <button type="submit" class="btn">Iniciar sesion</button>
                <div class="login-registrarse">
                    <p>No tiene una cuenta? <a href="" class="registrarse-link">Registrese aqui</a></p>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>