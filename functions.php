
<?php
function calendar()
{
    $mysqli = new mysqli("localhost", "root", "", "reserveringssysteem");
    $stmt = $mysqli->prepare("SELECT * FROM bookings where MONTH(date)=MONTH(now())
       and YEAR(date)=YEAR(now()) ");
    //Maak een array van deze data met de while loop
    $bookings = array();
    //if statement maken die de resultaten ophaalt van de query.
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            //associative array maken met datum.
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }
    // Tijdzone neerzetten voor de zekerheid
    date_default_timezone_set('Europe/Amsterdam');
    //year en month uit de database halen
    if (isset($_GET['ym'])) {
        $ym = $_GET['ym'];
    } else {
        // data van deze maand
        $ym = date('Y-m');
    }

    // Check format
    $timestamp = strtotime($ym . '-01');
    //zorgen dat altijd de juiste timestamp wordt weergegeven
    if ($timestamp === false) {
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');
    }

    // dag van vandaag in de format Year-Month-Day.
    $today = date('Y-m-j', time());

  // Dagen in een maand
    $day_count = date('t', $timestamp);

    // aantal dagen in de week
    $str = date('w', $timestamp);
    //Kalender maken
    $weeks = array();
    $week = '';
    //vak maken voor elke dag van de week met string repeat (hierdoor wordt er voor elke dag van de week een leeg vak gemaakt met html)
    $week .= str_repeat('<td></td>', $str);
    //boekingen tellen
    $bookingsAmount = array_count_values($bookings);

    //for loop maken die voor elke dag in de maand een vakje maakt
    for ($day = 1; $day <= $day_count; $day++, $str++) {
        $date = $ym . '-' . $day;
        $week .= '<td>' . $day;
        //knoppen toevoegen als de datum van de dag groter of gelijk is aan de dag van vandaag.
        if (strtotime($date) >= strtotime(date('Y-m-d'))) {
            if (!isset($bookingsAmount[$date]) || $bookingsAmount[$date] < 5) {
                $week .= "<center><a href='book.php?date=" . $date . "' class='btn btn-success btn-xs'>Book</a></center>";
            }
            // Als er meer dan 5 reserveringen op die dag zijn is het vol.
            else {
                $week .= "<center><button class='btn btn-danger btn-xs'>Vol!</button></center>";
            }
        }
        $week .= '</td>';

        // If statement maken die checkt of het het einde van de maand is en vervolgens een leeg vakje maakt voor de dagen die er nog in zouden passen. 
        if ($str % 7 == 6 || $day == $day_count) {
            // % teken hierboven zorgt ervoor dat er gecontroleerd word dat de week geeindigd is. Het pakt de remainder nadat je de 2 getallen gedeeld hebt als dat uberhaupt kan.
            if ($day == $day_count) {
                // voeg leeg vak toe om geen 32,33 enzo te krijgen
                $week .= str_repeat('<td></td>', 6 - ($str % 7));
            }

            $weeks[] = '<tr>' . $week . '</tr>';

            // Nieuwe week aanmaken
            $week = '';
        }
    }
    //calender laten zien door de variabele week te echo'en
    foreach ($weeks as $week) {
        echo $week;
    }
}
?>
