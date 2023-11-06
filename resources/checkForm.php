<?php
    session_start();
    if(isset($_SESSION['formFeedbackOK'])) {
        unset($_SESSION['formFeedbackOK']);
    }
    if(!isset($_POST['questionLang']) || !isset($_POST['questionLevel']) || !isset($_POST['question']) || !isset($_POST['correctAnswer']) 
    || !isset($_POST['incorrectAnswer1']) || !isset($_POST['incorrectAnswer2']) || !isset($_POST['incorrectAnswer3'])) {
        $_SESSION['formFeedback'] = "Si us plau omple tots els camps";
        header("Location: ../create.php");
        exit;
    }
    elseif(strlen(trim($_POST['questionLang'])) == 0 || strlen(trim($_POST['questionLevel'])) == 0 || strlen(trim($_POST['question'])) == 0 
        || strlen(trim($_POST['correctAnswer'])) == 0 || strlen(trim($_POST['incorrectAnswer1'])) == 0 || strlen(trim($_POST['incorrectAnswer2'])) == 0 || strlen(trim($_POST['incorrectAnswer3'])) == 0) {
            $_SESSION['formFeedback'] = "No es permet sols espais en blanc.";
            header("Location: ../create.php");
            exit;
    }
    
    elseif (strlen($_POST["question"]) > 116) {
        $_SESSION['formFeedback'] = "La pregunta no pot tindre més de 116 caràcters";
        header("Location: ../create.php");
        exit;
    }
    elseif (strlen($_POST["correctAnswer"]) > 40 || strlen($_POST["incorrectAnswer1"]) > 40 || strlen($_POST["incorrectAnswer2"]) > 40 || strlen($_POST["incorrectAnswer3"]) > 40) {
        $_SESSION['formFeedback'] = "Les respostes tenen un màxim de 40 caràcters.";
        header("Location: ../create.php");
        exit;
    }
    // Check the level value. If it is not between 1 and 6, the user is trying to fool us
    // Check the selected language. If it is not between the 3 languages specified in the array, the user is trying to fool us
    $correctLanguage = false;
    $languagesArray = ["catalan_", "english_", "spanish_"];
    foreach ($languagesArray as $lang) {
        if ($lang == $_POST['questionLang']) {
            $correctLanguage = true;
        }
    }
    $correctLevel = false;
    for ($i=1; $i <= 6; $i++) { 
        if ($i . "" == $_POST['questionLevel']) {
            $correctLevel = true;
            break;
        }
    }
    if (!$correctLevel || !$correctLanguage) {
        $_SESSION['formFeedback'] = "T'observem i no ens agrada que facis això.";
        header("Location: ../create.php");
        exit;
    }
    if(isset($_FILES['image'])) {
        $archivo = $_FILES['image'];
    
        // Comprueba si se subió un archivo
        if ($archivo['error'] === UPLOAD_ERR_OK) {
            // Comprueba si es una imagen
            $infoImagen = getimagesize($archivo['tmp_name']);
            if ($infoImagen !== false) {
                /* MIRAR ESTO
                // Ruta al archivo JSON
                $jsonFile = 'tu_archivo.json';

                // Lee el contenido actual del archivo JSON
                $jsonData = file_get_contents($jsonFile);

                // Decodifica el contenido JSON en un array asociativo
                $data = json_decode($jsonData, true);

                // Agrega nuevos datos al array
                $newData = array(
                    "Nueva Pregunta" => "Ruta/NuevaImagen.jpg",
                    "Otra Pregunta" => "Ruta/OtraImagen.jpg",
                    // Agrega todas las nuevas preguntas y rutas aquí
                );

                $data = array_merge($data, $newData);

                // Convierte el array asociativo en JSON nuevamente
                $jsonUpdated = json_encode($data, JSON_PRETTY_PRINT);

                // Escribe el JSON actualizado de vuelta al archivo
                file_put_contents($jsonFile, $jsonUpdated);

                echo "Contenido agregado al archivo JSON con éxito.";
                */
            } else {
                $_SESSION['formFeedback'] = "El fitxer no és una imatge vàlida.";
                header("Location: ../create.php");
                exit;
            }
        } else {
            $_SESSION['formFeedback'] = "Error al carregar la imatge";
            header("Location: ../create.php");
            exit;
        }
    }
    if(isset($_SESSION['formFeedback'])) {
        unset($_SESSION['formFeedback']);
    }

    $fileRoute = "../questions/".$_POST['questionLang'].$_POST['questionLevel'].".txt";
    $newFileContent = file_get_contents($fileRoute)."\n* ".$_POST['question']."\n+ ".$_POST['correctAnswer']."\n- ".$_POST['incorrectAnswer1']."\n- ".$_POST['incorrectAnswer2']."\n- ".$_POST['incorrectAnswer3'];
    file_put_contents($fileRoute, $newFileContent);
    $_SESSION['formFeedbackOK'] = "S'ha enregistrat la nova pregunta";
    header("Location: ../create.php");
    exit;
    
?>