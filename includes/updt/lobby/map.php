<?php 
$countrylist = [
    'black' => 'Jacata',
    'gray' => 'Ayacat',
    'red' => 'Reskat',
    'green' => 'Gatils',
    'yellow' => 'Ermao',
    'blue' => 'Stagat',
    'white' => 'Freezcat'
];
?>
<form id="countries" method="post">
    <img id="backgroundMapImg" class="countryImg" src="images/map/elements/base.jpg" />
    <?php 
    foreach ($countrylist as $color => $name) {
    ?>
    <img id="<?php echo $color ?>Img" class="countryImg" src="images/map/elements/<?php echo $color ?>/base.png" alt="Join <?php echo $color ?> team (<?php echo $name ?>)" />
    <?php 
    }
    ?>
</form>