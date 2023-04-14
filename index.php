<?php
session_start();

// Verificar si la sesión está vacía
if (empty($_SESSION['prompt'])) {
    // Definir prompt para el inicio de sesión
    $prompt = "necesito un resume sobre los delfines, un resume infantil";
} else {
    // Recuperar prompt de la sesión
    $prompt = $_SESSION['prompt'];
}

if (isset($_POST['titulo']) && isset($_POST['pregunta'])) {
    $prompt = $_POST['titulo'] . ". " . $_POST['pregunta'];
}

$apiKey = "API-KEY-OPENAI";
$url = "https://api.openai.com/v1/completions";

// Define headers
$headers = array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey,
    "OpenAI-Organization: NUMBER-ORG-OPENAI"
);

// Define data
$data = array(
    "model" => "text-davinci-003",
    "prompt" => $prompt,
    "temperature" => 0.5,
    "max_tokens" => 3999
);

// init curl
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// Execute request
$response = curl_exec($curl);

// Handle response
if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
} else {
    $response = json_decode($response);
    $message = $response->choices[0]->text;
}

curl_close($curl);
?>

<!DOCTYPE html>
<html>
<head>
    <title>GPT-3</title>
    
    //change tinyCME api key pls
    //cambia la api key de tiny en caso contrario no va
    <script src="https://cdn.tiny.cloud/1/v6bk2mkmbxcn1oybhyu2892lyn9zv/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
    tinymce.init({
        selector: 'textarea#respuesta',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ]
    });
  </script>
      <style>
      #loading {
        display: none;
        text-align: center;
        font-size: 20px;
        font-weight: bold;
      }
    </style>
</head>
<body>
<h1>GPT-3</h1>
<form method="POST">
    <label for="titulo">Título:</label>
    <br>
    <input type="text" name="titulo" id="titulo" required>
    <br><br>
    <label for="pregunta">Pregunta:</label>
    <br>
    <input type="text" name="pregunta" id="pregunta" required>
    <br><br>
    <button type="submit" onclick="startLoading();">Generar respuesta</button> <button id="stopBtn" disabled>Stop</button>
    <div id="loading">Loading...</div>
</form>
<button id="startBtn" hidden>Start</button>

    

    <script>
      var loading = document.getElementById("loading");
      var startBtn = document.getElementById("startBtn");
      var stopBtn = document.getElementById("stopBtn");
      var intervalId;

      function startLoading() {
        loading.style.display = "block";
        startBtn.disabled = true;
        stopBtn.disabled = false;
        intervalId = setInterval(function() {
          loading.innerHTML += ".";
        }, 500);
      }

      function stopLoading() {
        clearInterval(intervalId);
        loading.style.display = "none";
        loading.innerHTML = "Loading...";
        startBtn.disabled = false;
        stopBtn.disabled = true;
      }

      startBtn.addEventListener("click", startLoading);
      stopBtn.addEventListener("click", stopLoading);
    </script>

<br><br>
<?php if (isset($message)) { 
    //esto se usa para que cambie el espacio por </br>
    $texto_formateado = nl2br($message);?>
    <textarea id="respuesta"><?php echo "<center><b>".$_POST['titulo']."</b></center></br/</br>".htmlentities($texto_formateado, ENT_COMPAT, 'UTF-8') ?></textarea>
    <script>stopLoading();</script>
<?php } ?>
</body>
</html>


