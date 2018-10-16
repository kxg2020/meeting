<?php
if (! function_exists('mix')) {
    function mix($file_puth){
        if (!file_exists("mix-manifest.json")){
            return $file_puth;
        }
        $mixManifest = file_get_contents("mix-manifest.json");
        $mixManifest = json_decode($mixManifest);
        if (isset($mixManifest->$file_puth)){
            return $mixManifest->$file_puth;
        }
        return $file_puth;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>会议中心</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link href="<?php echo mix("/static/css/app.css"); ?>" rel="stylesheet">
</head>
<body>
<div id="app"></div>
</body>
<script>
  window.token = '{$token}'
  window.permission_ids = <?php echo json_encode($permission_ids)?>
</script>
<script type="text/javascript" src="<?php echo mix("/static/js/app.js"); ?>"></script>
</html>