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
    <!-- <button onclick="alert('<?php echo $color ?> - <?php echo $name ?>')" id="<?php echo $color ?>Btn" name="team" value="<?php echo $color ?>" class="countryBtn <?php echo $color ?>Team"> -->
        <img id="<?php echo $color ?>Img" class="countryImg" src="images/map/elements/<?php echo $color ?>/base.png" alt="Join <?php echo $color ?> team (<?php echo $name ?>)" />
    <!-- </button> -->
    <?php 
    }
    ?>
</form>