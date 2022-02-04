<?php

$success= '';
$error= '';
//met de database verbinden
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_NAME = 'phplogin';
$db = mysqli_connect($DATABASE_HOST, $DATABASE_USER, "", $DATABASE_NAME);
$id = $_SESSION['id']; //id halen van de login sessie

$query = mysqli_query($db,"select * from accounts where id='$id'"); // data van gebruiker ophalen
//data in een array stoppen.
$data = mysqli_fetch_array($query);
//username ophalen uit de array
$username = $data[1];
$email = $_SESSION['email'];

//verbinden met database
$mysqli = new mysqli('localhost', 'root', '', 'reserveringssysteem');
//kijken of er een datum is doorgegeven
if(isset($_GET['date'])){
    $date = $_GET['date'];
    //data ophalen uit de tabel
    $stmt = $mysqli->prepare("select * from bookings where date = ?");
    //string maken van de data om een sql injectie te voorkomen
    $stmt->bind_param('s', $date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
    }
}
// voorkomen dat mensen bepaalde data reserveren die niet meer beschikbaar zijn.
if (strtotime($date) < strtotime(date('Y-m-d'))){
    if (isset($bookingsAmount[$date]) || $bookingsAmount[$date] >= 5) {
    header('location: reserveren.php');
    }
}
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $mysqli->prepare("select * from bookings where date = ?");
    $stmt->bind_param('s', $date);
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            $error = 'al gereserveerd'; 
        } else {
       // sql injecties tegenhouden door een prepared statement te gebruiken. Hierdoor wordt het statement al uitgevoerd voordat er een request is gedaan.
            $stmt = $mysqli->prepare("INSERT INTO bookings (name, email, date) VALUES (?,?,?)");
            //van alle data een string maken om een sql injectie te voorkomen
            $stmt->bind_param('sss', $name, $email, $date);
            $stmt->execute();
            $success = 'Booking Successfull';
            $stmt->close();
            $mysqli->close();
        }

        }
    }


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://i.postimg.cc/QNpWgb6Y/ja-removebg-preview.png">

    <title>Reserveren</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet"  href="secure.css">
</head>

<body>
<center>
        <div class="edit">
        <h1 class="text-center">Reserveren voor datum <?php echo $date ?></h1>
                <form action="" method="post">
                    <div class="input">
                        <h3 class="success"> <?= $success ?></h3>
                        <h3 class="fault"> <?= $error ?> </h3>
                        <label for="">Naam</label>
                        <input required type="username" class="form-control" value="<?php echo $username; ?>"
                            name="name">
                        </h3>
                    </div>
                    <div class="input">
                        <label for="">Email</label>
                        <input required type="email" class="form-control" value="<?php echo $email; ?>" name="email">
                    </div>
                    <div class="form-group">
                        <button name="submit" type="submit" class="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div> </center>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
    </script>
</body>

</html>
