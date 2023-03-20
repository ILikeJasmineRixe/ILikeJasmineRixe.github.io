<?php
// Initialize session
session_start();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['create'])) {
    // Create a new room
    $room_pin = generate_room_pin();
    $room_id = uniqid();
    $room_data = array(
      'id' => $room_id,
      'pin' => $room_pin,
      'users' => array(),
      'messages' => array()
    );
    save_room_data($room_id, $room_data);
    $_SESSION['room_id'] = $room_id;
    $_SESSION['user_id'] = uniqid();
    header("Location: chat.php");
    exit();
  } else if (isset($_POST['join'])) {
    // Join an existing room
    $room_pin = $_POST['join'];
    $room_id = get_room_id_by_pin($room_pin);
    if ($room_id) {
      $_SESSION['room_id'] = $room_id;
      $_SESSION['user_id'] = uniqid();
      header("Location: chat.php");
      exit();
    } else {
      $error_msg = "Invalid room pin";
    }
  }
}

// Helper functions for creating/joining rooms
function generate_room_pin() {
  // Generate a random 6-digit pin with a lowercase letter
  $digits = str_split('0123456789');
  $letters = str_split('abcdefghijklmnopqrstuvwxyz');
  shuffle($digits);
  shuffle($letters);
  $pin = implode(array_slice($digits, 0, 6)) . $letters[0];
  return $pin;
}

function save_room_data($room_id, $room_data) {
  // Save the room data to a JSON file
  $filename = "rooms/$room_id.json";
  file_put_contents($filename, json_encode($room_data));
}

function get_room_id_by_pin($room_pin) {
  // Look up the room ID by its pin
  $files = glob("rooms/*.json");
  foreach ($files as $file) {
    $data = json_decode(file_get_contents($file), true);
    if ($data['pin'] === $room_pin) {
      return $data['id'];
    }
  }
  return null;
}

// Display the main page
?>
<!DOCTYPE html>
<html>
<head>
  <title>Chat Room</title>
  <link rel="stylesheet" href="style.css">

</head>
<body>
  <h1>Chat Room</h1>
  <?php if (isset($error_msg)) { ?>
    <p style="color: red;"><?php echo $error_msg; ?></p>
  <?php } ?>
  <form method="POST">
    <label for="join">Join a chat room:</label>
    <input type="text" id="join" name="join" placeholder="Enter room pin">
    <button type="submit">Join</button>
  </form>
  <hr>
  <form method="POST">
    <label for="create">Create a new chat room:</label>
    <button type="submit" id="create" name="create">Create</button>
  </form>
</body>
</html>
