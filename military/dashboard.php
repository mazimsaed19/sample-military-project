<?php 
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$total = $conn->query("SELECT COUNT(*) AS total FROM soldiers")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Dashboard - Soldier Management</title>
<link rel="stylesheet" href="css/style.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container">
  <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>

  <div class="dashboard-flex">

    <!-- Actions Panel (Left) -->
    <div class="panel actions">
      <h2>⚙️ Actions</h2>
      <ul>
        <li><a href="add_soldier.php">➕ Add Soldier</a></li>
        <li><a href="list_soldiers.php">📋 Manage Soldiers</a></li>
        <li><a href="report_soldiers.php">📄 Reports</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
      </ul>
    </div>

    <!-- Overview Panel (Center) -->
    <div class="panel overview">
      <h2>📊 Overview</h2>
      <p><strong>Total Soldiers:</strong> <?= $total ?></p>

      <h3>🪖 By Unit</h3>
      <ul>
        <?php
          $unitStats = $conn->query("SELECT unit, COUNT(*) AS count FROM soldiers GROUP BY unit");
          while ($u = $unitStats->fetch_assoc()):
        ?>
          <li><?= htmlspecialchars($u['unit']) ?: 'Unspecified' ?>: <?= $u['count'] ?></li>
        <?php endwhile; ?>
      </ul>

      <h3>🎖️ By Rank</h3>
      <ul>
        <?php
          $rankStats = $conn->query("SELECT rank, COUNT(*) AS count FROM soldiers GROUP BY rank");
          while ($r = $rankStats->fetch_assoc()):
        ?>
          <li><?= htmlspecialchars($r['rank']) ?: 'Unspecified' ?>: <?= $r['count'] ?></li>
        <?php endwhile; ?>
      </ul>
    </div>

    <!-- Charts Panel (Right) -->
    <div class="panel charts">
      <h2>📈 Charts</h2>
      <canvas id="unitChart"></canvas>
      <canvas id="rankChart"></canvas>
    </div>

  </div>
</div>

<script>
  // UNIT CHART
  const unitCtx = document.getElementById('unitChart').getContext('2d');
  const unitChart = new Chart(unitCtx, {
    type: 'bar',
    data: {
      labels: [<?php
        $unitLabels = $conn->query("SELECT unit FROM soldiers GROUP BY unit");
        while ($u = $unitLabels->fetch_assoc()) echo "'".addslashes($u['unit'])."',";
      ?>],
      datasets: [{
        label: 'Soldiers per Unit',
        data: [<?php
          $unitCounts = $conn->query("SELECT COUNT(*) as count FROM soldiers GROUP BY unit");
          while ($c = $unitCounts->fetch_assoc()) echo "{$c['count']},";
        ?>],
        backgroundColor: ['#5a7552', '#708a67', '#9aa57c', '#4f6a4d'],
        borderColor: '#3b4f3b',
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: { labels: { color: '#b4d59e' } }
      },
      scales: {
        y: { beginAtZero: true, ticks: { color: '#fff' } },
        x: { ticks: { color: '#fff' } }
      }
    }
  });

  // RANK CHART
  const rankCtx = document.getElementById('rankChart').getContext('2d');
  const rankChart = new Chart(rankCtx, {
    type: 'pie',
    data: {
      labels: [<?php
        $rankLabels = $conn->query("SELECT rank FROM soldiers GROUP BY rank");
        while ($r = $rankLabels->fetch_assoc()) echo "'".addslashes($r['rank'])."',";
      ?>],
      datasets: [{
        label: 'Soldiers per Rank',
        data: [<?php
          $rankCounts = $conn->query("SELECT COUNT(*) as count FROM soldiers GROUP BY rank");
          while ($c = $rankCounts->fetch_assoc()) echo "{$c['count']},";
        ?>],
        backgroundColor: ['#3f6846', '#779c68', '#b4cc9a', '#2f4f3e', '#4a7555'],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: { labels: { color: '#fff' } },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.label || '';
              let value = context.parsed || 0;
              let dataset = context.dataset;
              let total = dataset.data.reduce((a, b) => a + b, 0);
              let percentage = total ? ((value / total) * 100).toFixed(1) : 0;
              return label + ': ' + value + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });
</script>

</body>
</html>
