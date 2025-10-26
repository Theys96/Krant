<?php
/**
 * @var int                                    $highscore_small
 * @var int                                    $highscore_big
 * @var array<array<array{0: string, 1: int}>> $topFive
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
echo "<input type='hidden' id='highscore_small' value='$highscore_small'/>";
echo "<input type='hidden' id='highscore_big' value='$highscore_big'/>";
echo "<input type='hidden' id='topFive' value='".json_encode($topFive)."'/>";
?>

<script src="assets\vendor\jquery\jquery.min.js"></script>
<script>
  const canvas = document.getElementById("gameCanvas");
  const ctx = canvas.getContext("2d");

  // Stel canvasgrootte in
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  
  //decide highscore mode
  const isSmall = canvas.width * canvas.height < 600000;

  // Variabelen voor vijandgrootte, spawnmarge en spelerafmetingen
  const enemySize = 120;        // Basisgrootte van vijand
  const friendSize = 120;        // Basisgrootte van vrienden
  const spawnMargin = 50;      // Marge van rand voor vijanden
  const playerSize = 90;       // Basisgrootte van de spelerafbeelding

  // Variabelen voor het geleidelijk moeilijker maken.
  const scalar = 100;

  // Counters
  let score = 0;
  let enemyCount = 0;
  let kills = 0;

  //gamestate
  let gameState = 0;  // 0 = active, 1 = paused, 2 = game over
  let soundOn = true;
  const enemyLimit = 10;
  let highscore = 0;
  if (isSmall) {
    highscore = document.getElementById("highscore_small").value;
  } else {
    highscore = document.getElementById("highscore_big").value;
  }
  let oldhighscore = highscore;
  let topFive = JSON.parse(document.getElementById("topFive").value);
  if (isSmall) {
    topFive = topFive[0];
  } else {
    topFive = topFive[1];
  }

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
    new Audio('assets/audio/laser3.mp3'),
    new Audio('assets/audio/laser4.mp3'),
    new Audio('assets/audio/laser5.mp3')
  ];
  const friendHitSound = [
    new Audio('assets/audio/wilhelm.mp3')
  ]
  let currentAudio = laserSounds[0];

  //menu afbeeldingen
  const pauseImage = new Image();
  pauseImage.src = "assets/img/pause.png";
  const playImage = new Image();
  playImage.src = "assets/img/play.png";
  const muteImage = new Image();
  muteImage.src = "assets/img/Mute_Icon.png";
  const soundImage = new Image();
  soundImage.src = "assets/img/Speaker_Icon.png";

  // Vijand afbeelding
  const enemyImage = new Image();
  enemyImage.src = "assets/img/MHN.png"; // Vervang door je eigen vijandafbeelding!

  // Vijand afbeelding
  const friendImage = new Image();
  friendImage.src = "assets/img/NHW.png"; // Vervang door je eigen vriendafbeelding!

  // Functie om een nieuwe vijand toe te voegen
  function addEnemy() {
    const spawnChance = Math.random();
    const enemyCountToAdd = spawnChance < (0.03 * (1 + kills/scalar)) ? 4 : spawnChance < (0.14 * (1 + kills/scalar))? 2 : 1; // 1% kans op 5 vijanden, 9% kans op 2 vijanden

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
      gameState = 2;
    }
  }

  // Functie om een nieuwe vrienden toe te voegen
  function addFriend() {
    const spawnChance = Math.random();
    const friendCountToAdd = spawnChance < (0.02 * (1 + kills/scalar)) ? 2 : spawnChance < (0.1 * (1 + kills/scalar)) ? 1 : 0; // 1% kans op 2 vrienden, 9% kans op 1 vriend

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
    const friendCountToRemove = removeChance < (0.1 * (1 + kills/scalar)) ? 2 : removeChance < (0.2 * (1 + kills/scalar)) ? 1 : 0; // 1% kans op 2 vrienden, 9% kans op 1 vriend

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
    
    if (gameState == 0) {
      for (let i = enemies.length - 1; i >= 0; i--) {
        const enemy = enemies[i];
        const isHit =
          clickX > enemy.x - enemy.width / 2 &&
          clickX < enemy.x + enemy.width / 2 &&
          clickY > enemy.y - enemy.height / 2 &&
          clickY < enemy.y + enemy.height / 2;

        if (isHit) {
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

          if (isHit) {
            shootLaser(friend, 1);
            hit = true;
            break; // Stop met het controleren van andere vijanden na het raken
          }
        }
      }
    }

    if (gameState != 2 && !hit) {
      const togglesound = 
        clickX > canvas.width - 50 && 
        clickX < canvas.width &&
        clickY < 90 && clickY > 40;

      if (togglesound) {
        soundOn = soundOn == false;
      }
      const pause = 
        clickX > canvas.width - 100 && 
        clickX < canvas.width - 50 &&
        clickY < 90 && clickY > 40;

      if (pause) {
        gameState = gameState == 1 ? 0 : 1;
      }
    }
    //retry knop om te herstarten
    if (gameState == 2) {
      const retry =
        clickX > (canvas.width / 2) - 100 && 
        clickX < ((canvas.width / 2) - 100) + 200 &&
        clickY > (canvas.height / 2) + 148 &&
        clickY < ((canvas.height / 2) + 148) + 50;

      if (retry) {
        score = 0;
        kills = 0;
        enemies = [];
        friends = [];
        gameState = 0;
        updateGame();
        startSpawning();
      }
    }
  });

  // speel een random geluid uit een array af.
  function playSound(soundList) {
    if (soundOn && soundList.length > 0) {
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
    target.alive = false;
    if (int == 0) {
      playSound(laserSounds);
      score++; // vijand geraakt, Verhoog de kill counter
    } else {
      playSound(friendHitSound);
      score = score - 5; //vriend geraakt, verlaag counter
    }
    kills++;
    if (score > oldhighscore) {
      highscore = score;
    } else {
      highscore = oldhighscore;
    }
  }

  // Functie om willekeurige spawn-tijden te krijgen tussen 0,7 en 2 seconden
  function randomSpawnTime() {
    return Math.random() * (2400 - 700) * (scalar/(scalar + kills)) + 700; // Geeft tijd in milliseconden
  }

  // Spawn vijanden met willekeurige intervallen
  function startSpawning() {
    setTimeout(() => {
      if (gameState == 0) {
        addEnemy();
        removeFriend();
        addFriend();
      }
      if (gameState != 2) {
        startSpawning(); // Herstart de spawnfunctie na een willekeurige tijd
      }
    }, randomSpawnTime());
  }

  // Update highscore van de gebruiker totdat de request lukt
  function updateHighscore() {
    setTimeout(() => {
      if(highscore > oldhighscore) {
        $.post("?action=minigame", {highscore: highscore, is_small: isSmall}, function(data) {
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
    ctx.fillText("Raak geen NHW!", 10, 90);
    ctx.fillText("Score: " + score, 10, 120);
    ctx.fillText("Nachtmeries: " + enemyCount, 10, 150);
    //modus
    if (isSmall) {
      ctx.fillText("Kleine modus", canvas.width - ctx.measureText("Kleine modus").width - 15, 30);
    } else {
      ctx.fillText("Grote modus",  canvas.width - ctx.measureText("Grote modus").width - 15, 30);
    }
    //dynamische knopjes voor pauzeren en geluid
    if (gameState == 1) { //gepauzeerd
      ctx.drawImage(playImage, canvas.width -100, 40, 25, 25);
    } else {
      ctx.drawImage(pauseImage, canvas.width -100, 40, 25, 25);
    }
    if (soundOn) {
      ctx.drawImage(soundImage, canvas.width -50, 40, 25, 25);
    } else {
      ctx.drawImage(muteImage, canvas.width -50, 40, 25, 25);
    }
    ctx.fillText("Highscore:", canvas.width - 110, 90);
    ctx.fillText(highscore, canvas.width - ctx.measureText(highscore).width - 15, 120);

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
    //game paused overlay
    if (gameState == 1) {
      ctx.fillStyle = "rgb(32,32,32,0.4)";
      ctx.fillRect(0 , 0, canvas.width, canvas.height);
      ctx.fillStyle = "white";
      ctx.font = "65px Arial";
      ctx.fillText("GEPAUZEERD", (canvas.width / 2) - ctx.measureText("GEPAUZEERD").width / 2, (canvas.height / 2) - 100);
    }
    //game over overlay
    if (gameState == 2) {
      ctx.fillStyle = "rgb(32,32,32,0.4)";
      ctx.fillRect(0 , 0, canvas.width, canvas.height);
      ctx.fillStyle = "white";
      ctx.fillRect((canvas.width / 2) - 102, (canvas.height / 2) + 148, 204, 54);
      ctx.fillStyle = "gray";
      ctx.fillRect((canvas.width / 2) - 100, (canvas.height / 2) + 150, 200, 50);
      ctx.fillStyle = "white";
      ctx.font = "65px Arial";
      ctx.fillText("GAME OVER", (canvas.width / 2) - ctx.measureText("GAME OVER").width / 2, (canvas.height / 2) - 160);
      ctx.font = "30px Arial";
      ctx.fillText("Score: " + score, (canvas.width / 2) - ctx.measureText("Score: " + score).width / 2, (canvas.height / 2) - 120);
      ctx.font = "40px Arial";
      ctx.fillText("Opnieuw", (canvas.width / 2) - ctx.measureText("Opnieuw").width / 2, (canvas.height / 2) + 187);
      renderScores();
      if (highscore > oldhighscore) {
        $.post("?action=minigame", {highscore: highscore,  is_small: isSmall}, function(data) {
          oldhighscore = highscore;
        })
        updateHighscore();
      }
    }
    if (gameState != 2) {
      requestAnimationFrame(updateGame);
    }
  }

  // Tekent de top 5 highscores plus de gebruikers score op het midden van het scherm
  function renderScores() {
    const newTopFive = topFive.slice()
    const rank = newTopFive.findIndex(entry => highscore > entry[1]);
    if (rank !== -1) {
      newTopFive.splice(rank, 0, ['JIJ', highscore]);
      if (newTopFive.length > 5) {
        newTopFive.pop();
      }
    }

    ctx.font = "50px Arial";
    ctx.fillText("Highscores", (canvas.width / 2) - ctx.measureText("Highscores").width / 2, (canvas.height / 2) - 60);
    let offset = -20;
    let text = "";
    ctx.font = "20px Arial";
    for (let i = 0; i < newTopFive.length; i++) {
      text = (i + 1) + ". " + newTopFive[i][0] + ": " + newTopFive[i][1];
      ctx.fillText(text, (canvas.width / 2) - ctx.measureText(text).width / 2, (canvas.height / 2) + offset);
      offset = offset + 30;
    }
    if (rank === -1) {
      if (newTopFive.length === 5) {
        text = "-  JIJ: " + highscore;
      } else {
        text = (newTopFive.length + 1) + ". JIJ: " + highscore;
      }
      ctx.fillText(text, (canvas.width / 2) - ctx.measureText(text).width / 2, (canvas.height / 2) + offset);
    }
  }

  // Start het spel
  updateGame();
  startSpawning(); // Start vijand spawning met willekeurige intervallen

</script>
</body>
</html>
