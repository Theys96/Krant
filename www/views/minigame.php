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

<script>
  const canvas = document.getElementById("gameCanvas");
  const ctx = canvas.getContext("2d");

  // Stel canvasgrootte in
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  // Variabelen voor vijandgrootte, spawnmarge en spelerafmetingen
  const enemySize = 120;        // Basisgrootte van vijand
  const spawnMargin = 50;      // Marge van rand voor vijanden
  const playerSize = 90;       // Basisgrootte van de spelerafbeelding


  // Counters
  let killCount = 0;
  let enemyCount = 0;

  // Speler object met afbeelding
  const player = {
    x: canvas.width / 2,
    y: canvas.height / 2,
    angle: 0,
    size: playerSize,
  };

  // Speler afbeelding
  const playerImage = new Image();
  playerImage.src = "/assets/img/thijs.png"; // Vervang door jouw eigen spelerafbeelding!

  // Array voor vijanden en lasers
  let enemies = [];
  let lasers = [];

  // Vijand afbeelding
  const enemyImage = new Image();
  enemyImage.src = "/assets/img/MHN.png"; // Vervang door je eigen vijandafbeelding!

  // Functie om een nieuwe vijand toe te voegen
  function addEnemy() {
    const spawnChance = Math.random();
    const enemyCountToAdd = spawnChance < 0.03 ? 6 : spawnChance < 0.14 ? 2 : 1; // 1% kans op 5 vijanden, 9% kans op 2 vijanden

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
  }

  // Zorg dat er altijd minstens één vijand is
  function ensureMinimumEnemies() {
    if (enemies.length === 0) {
      addEnemy();
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

    for (let i = enemies.length - 1; i >= 0; i--) {
      const enemy = enemies[i];
      const isHit =
        clickX > enemy.x - enemy.width / 2 &&
        clickX < enemy.x + enemy.width / 2 &&
        clickY > enemy.y - enemy.height / 2 &&
        clickY < enemy.y + enemy.height / 2;

      if (isHit) {
        shootLaser(enemy);
        break; // Stop met het controleren van andere vijanden na het raken
      }
    }
  });

  // Functie om laser te schieten en kill counter bij te werken
  function shootLaser(enemy) {
    lasers.push({ x: enemy.x, y: enemy.y, timestamp: Date.now() });
    enemy.alive = false;
    killCount++; // Verhoog de kill counter

    // Zorg ervoor dat er direct een nieuwe vijand verschijnt
    ensureMinimumEnemies(0);
  }

  // Functie om willekeurige spawn-tijden te krijgen tussen 0,7 en 2 seconden
  function randomSpawnTime() {
    return Math.random() * (1600 - 700) + 700; // Geeft tijd in milliseconden
  }

  // Spawn vijanden met willekeurige intervallen
  function startEnemySpawning() {
    setTimeout(() => {
      addEnemy();
      startEnemySpawning(); // Herstart de spawnfunctie na een willekeurige tijd
    }, randomSpawnTime());
  }

  // Update-functie voor spel
  function updateGame() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Kill counter en vijandenteller weergeven
    ctx.fillStyle = "white";
    ctx.font = "20px Arial";
    ctx.fillText("Kills: " + killCount, 10, 30);
    ctx.fillText("Nachtmeries: " + enemyCount, 10, 60);

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

    // Zorg ervoor dat er altijd minstens één vijand is
    ensureMinimumEnemies();

    requestAnimationFrame(updateGame);
  }

  // Start het spel
  updateGame();
  startEnemySpawning(); // Start vijand spawning met willekeurige intervallen

</script>

</body>
</html>
