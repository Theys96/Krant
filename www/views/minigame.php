<?php
/**
 * @var int    $highscore
 * @var string $topFive
 */
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Krant | Thijs zijn nachtmerrie</title>
  <style>
    body, html {
      margin: 0;
      overflow: hidden;
    }
    canvas {
      background-color: #111;
    }
  </style>
</head>
<body>

<canvas id="gameCanvas"></canvas>

<?php
// hidden input om deze variabelen in het js script te kunnen pakken
echo "<input type='hidden' id='highscore' value='$highscore'/>";
echo "<input type='hidden' id='topFive' value='$topFive'/>";
?>

<script src="assets\vendor\jquery\jquery.min.js"></script>
<script>
  const canvas = document.getElementById("gameCanvas");
  const ctx = canvas.getContext("2d");

  // Stel canvasgrootte in
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  // Variabelen voor vijandgrootte, spawnmarge en spelerafmetingen
  const enemySize = 120;        // Basisgrootte van vijand
  const friendSize = 120;        // Basisgrootte van vrienden
  const spawnMargin = 50;      // Marge van rand voor vijanden
  const playerSize = 90;       // Basisgrootte van de spelerafbeelding

  // Variabelen voor het geleidelijk moeilijker maken.
  const scalar = 100;

  // Counters
  let killCount = 0;
  let enemyCount = 0;
  let totalKills = 0;

  //gamestate
  let gameActive = true;
  const enemyLimit = 10;
  let highscore = document.getElementById("highscore").value;
  let oldhighscore = highscore;
  let topFive = JSON.parse(document.getElementById("topFive").value);

  // Speler object met afbeelding
  const player = {
    x: canvas.width / 2,
    y: canvas.height / 2,
    angle: 0,
    size: playerSize,
  };

  // Speler afbeelding
  const playerImage = new Image();
  playerImage.src = "assets/img/thijs.png"; // Vervang door jouw eigen spelerafbeelding!

  // Array voor vijanden en lasers en vrienden
  let enemies = [];
  let lasers = [];
  let friends = [];

  //audio
  const laserSounds = [
    new Audio('assets/audio/laser1.mp3'),
    new Audio('assets/audio/laser2.mp3'),
    new Audio('assets/audio/laser3.mp3')
  ];
  let currentAudio = laserSounds[0];

  // Vijand afbeelding
  const enemyImage = new Image();
  enemyImage.src = "assets/img/MHN.png"; // Vervang door je eigen vijandafbeelding!

  // Vijand afbeelding
  const friendImage = new Image();
  friendImage.src = "assets/img/NHW.png"; // Vervang door je eigen vriendafbeelding!

  // Functie om een nieuwe vijand toe te voegen
  function addEnemy() {
    const spawnChance = Math.random();
    const enemyCountToAdd = spawnChance < (0.03 * (1 + totalKills/scalar)) ? 4 : spawnChance < (0.14 * (1 + totalKills/scalar))? 2 : 1; // 1% kans op 5 vijanden, 9% kans op 2 vijanden

    for (let i = 0; i < enemyCountToAdd; i++) {
      const enemy = {
        x: spawnMargin + Math.random() * (canvas.width - 2 * spawnMargin),
        y: spawnMargin + Math.random() * (canvas.height - 2 * spawnMargin),
        width: enemySize,
        height: enemySize * (enemyImage.height / enemyImage.width), // Houdt de verhoudingen
        alive: true,
      };
      enemies.push(enemy);
    }

    enemyCount = enemies.length; // Update het aantal vijanden op het speelveld
    if (enemyCount > enemyLimit) {
      gameActive = false;
    }
  }

  // Functie om een nieuwe vrienden toe te voegen
  function addFriend() {
    const spawnChance = Math.random();
    const friendCountToAdd = spawnChance < (0.03 * (1 + totalKills/scalar)) ? 2 : spawnChance < (0.14 * (1 + totalKills/scalar)) ? 1 : 0; // 1% kans op 2 vrienden, 9% kans op 1 vriend

    for (let i = 0; i < friendCountToAdd; i++) {
      const friend = {
        x: spawnMargin + Math.random() * (canvas.width - 2 * spawnMargin),
        y: spawnMargin + Math.random() * (canvas.height - 2 * spawnMargin),
        width: friendSize,
        height: friendSize * (friendImage.height / friendImage.width), // Houdt de verhoudingen
        alive: true,
      };
      friends.push(friend);
    }
  }

  // Functie om een vrienden te verwijderen
  function removeFriend() {
    const removeChance = Math.random();
    const friendCountToRemove = removeChance < (0.1 * (1 + totalKills/scalar)) ? 2 : removeChance < (0.2 * (1 + totalKills/scalar)) ? 1 : 0; // 1% kans op 2 vrienden, 9% kans op 1 vriend

    for (let i = 0; i < friendCountToRemove; i++) {
      if(friends.length > 0) {
        let index = Math.floor(Math.random() * friends.length);
        friends[index].alive = false;
      }
    }
  }

  // Functie om de hoek naar de muis te berekenen
  function getAngleToMouse(x, y) {
    const dx = x - player.x;
    const dy = y - player.y;
    return Math.atan2(dy, dx);
  }

  // Muismove event
  let mouseX = canvas.width / 2;
  let mouseY = canvas.height / 2;

  canvas.addEventListener("mousemove", (event) => {
    mouseX = event.clientX;
    mouseY = event.clientY;
    player.angle = getAngleToMouse(mouseX, mouseY);
  });

  // Vijand klikken
  canvas.addEventListener("click", (event) => {
    const clickX = event.clientX;
    const clickY = event.clientY;
    let hit = false;

    for (let i = enemies.length - 1; i >= 0; i--) {
      const enemy = enemies[i];
      const isHit =
        clickX > enemy.x - enemy.width / 2 &&
        clickX < enemy.x + enemy.width / 2 &&
        clickY > enemy.y - enemy.height / 2 &&
        clickY < enemy.y + enemy.height / 2;

      if (isHit && gameActive) {
        shootLaser(enemy, 0);
        hit = true;
        break; // Stop met het controleren van andere vijanden na het raken
      }
    }
    if (!hit) {
      for (let i = friends.length - 1; i >= 0; i--) {
        const friend = friends[i];
        const isHit =
          clickX > friend.x - friend.width / 2 &&
          clickX < friend.x + friend.width / 2 &&
          clickY > friend.y - friend.height / 2 &&
          clickY < friend.y + friend.height / 2;

        if (isHit && gameActive) {
          shootLaser(friend, 1);
          break; // Stop met het controleren van andere vijanden na het raken
        }
      }
    }

    //retry knop om te herstarten
    if (!gameActive) {
      const retry =
        clickX > (canvas.width / 2) - 100 && 
        clickX < ((canvas.width / 2) - 100) + 200 &&
        clickY > (canvas.height / 2) + 128 &&
        clickY < ((canvas.height / 2) + 128) + 50;

      if (retry) {
        location.reload();
      }
    }
  });

  // speel een random geluid uit een array af.
  function playSound(soundList) {
    if (soundList.length > 0) {
      const index = Math.floor(Math.random() * soundList.length);
      currentAudio.pause();
      currentAudio.currentTime = 0;
      currentAudio = soundList[index];
      currentAudio.play();
    }
  }

  // Functie om laser te schieten en kill counter bij te werken
  function shootLaser(target, int) {
    lasers.push({ x: target.x, y: target.y, timestamp: Date.now() });
    playSound(laserSounds);
    target.alive = false;
    if (int == 0) {
      killCount++; // vijand geraakt, Verhoog de kill counter
    } else {
      killCount = killCount - 5; //vriend geraakt, verlaag counter
    }
    totalKills++;
    if (killCount > highscore) {
      highscore = killCount;
    }
  }

  // Functie om willekeurige spawn-tijden te krijgen tussen 0,7 en 2 seconden
  function randomSpawnTime() {
    return Math.random() * (2400 - 700) * (scalar/(scalar + totalKills)) + 700; // Geeft tijd in milliseconden
  }

  // Spawn vijanden met willekeurige intervallen
  function startSpawning() {
    setTimeout(() => {
      addEnemy();
      removeFriend();
      addFriend();
      if (gameActive) {
        startSpawning(); // Herstart de spawnfunctie na een willekeurige tijd
      }
    }, randomSpawnTime());
  }

  // Update highscore van de gebruiker totdat de request lukt
  function updateHighscore() {
    setTimeout(() => {
      if(highscore > oldhighscore) {
        $.post("?action=minigame", {highscore: highscore}, function(data) {
          oldhighscore = highscore;
        })
        updateHighscore();
      }
    }, 3000);
  }

  // Update-functie voor spel
  function updateGame() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Kill counter en vijandenteller weergeven
    ctx.fillStyle = "white";
    ctx.font = "20px Arial";
    ctx.fillText("Krijg niet meer dan", 10, 30);
    ctx.fillText(enemyLimit + " MHN nachtmerries!", 10, 60);
    ctx.fillText("Kills: " + killCount, 10, 90);
    ctx.fillText("Nachtmeries: " + enemyCount, 10, 120);
    ctx.fillText("Highscore:", canvas.width - 110, 30);
    ctx.fillText(highscore, canvas.width - ctx.measureText(highscore).width - 15, 60);

    // Tekent speler als afbeelding met centrering
    ctx.save();
    ctx.translate(player.x, player.y);
    ctx.rotate(player.angle);
    ctx.drawImage(
      playerImage,
      -player.size / 1.8, // Correct centreren van de afbeelding
      -player.size / 1,
      player.size,
      player.size * (playerImage.height / playerImage.width) // Schaal de hoogte naar de breedte

    );
    ctx.restore();

    // Tekent vijanden als afbeeldingen met behoud van verhouding
    friends = friends.filter(friend => friend.alive);
    friends.forEach(friend => {
      ctx.drawImage(
        friendImage,
        friend.x - friend.width / 2,
        friend.y - friend.height / 2,
        friend.width,
        friend.height
      );
    });

    // Tekent vijanden als afbeeldingen met behoud van verhouding
    enemies = enemies.filter(enemy => enemy.alive);
    enemies.forEach(enemy => {
      ctx.drawImage(
        enemyImage,
        enemy.x - enemy.width / 2,
        enemy.y - enemy.height / 2,
        enemy.width,
        enemy.height
      );
    });
    
    enemyCount = enemies.length; // Update de vijandenteller

    // Tekent lasers
    ctx.strokeStyle = "red";
    ctx.lineWidth = 4;
    const currentTime = Date.now();
    lasers = lasers.filter(laser => currentTime - laser.timestamp < 200); // Houd lasers 0,2 seconden zichtbaar
    lasers.forEach(laser => {
      ctx.beginPath();
      ctx.moveTo(player.x, player.y);
      ctx.lineTo(laser.x, laser.y);
      ctx.stroke();
    });

    //game over overlay
    if (!gameActive) {
      ctx.fillStyle = "rgb(32,32,32,0.4)";
      ctx.fillRect(0 , 0, canvas.width, canvas.height);
      ctx.fillStyle = "white";
      ctx.fillRect((canvas.width / 2) - 102, (canvas.height / 2) + 128, 204, 54);
      ctx.fillStyle = "gray";
      ctx.fillRect((canvas.width / 2) - 100, (canvas.height / 2) + 130, 200, 50);
      ctx.fillStyle = "white";
      ctx.font = "65px Arial bold";
      ctx.fillText("GAME OVER", (canvas.width / 2) - ctx.measureText("GAME OVER").width / 2, (canvas.height / 2) - 150);
      ctx.font = "50px Arial bold";
      ctx.fillText("retry", (canvas.width / 2) - ctx.measureText("retry").width / 2, (canvas.height / 2) + 167);
      renderScores();
      if (highscore > oldhighscore) {
        $.post("?action=minigame", {highscore: highscore}, function(data) {
          oldhighscore = highscore;
        })
        updateHighscore();
      }
    }
    if (gameActive) {
      requestAnimationFrame(updateGame);
    }
  }

  //tekent de top 5 highscores plus de gebruikers score op het midden van het scherm
  function renderScores(){
    ctx.font = "50px Arial bold";
    ctx.fillText("Highscores", (canvas.width / 2) - ctx.measureText("Highscores").width / 2, (canvas.height / 2) - 90);
    offset = -50;
    j = 0;
    text = "";
    ctx.font = "20px Arial bold";
    for (let i = 1; i < 6; i++) {
      if (j==i - 1 && highscore > Number(topFive[j][1])) {
        text = i + ". YOU: " + highscore;
      } else {
        text = i + ". " + topFive[j][0] + ": " + topFive[j][1];
        j++;
      }
      ctx.fillText(text, (canvas.width / 2) - ctx.measureText(text).width / 2, (canvas.height / 2) + offset);
      offset = offset + 30;
    }
    if (j == 5) {
      text = "-  YOU: " + highscore;
      ctx.fillText(text, (canvas.width / 2) - ctx.measureText(text).width / 2, (canvas.height / 2) + offset);
    }
  }

  // Start het spel
  updateGame();
  startSpawning(); // Start vijand spawning met willekeurige intervallen

</script>
</body>
</html>
