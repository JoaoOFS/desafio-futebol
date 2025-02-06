<?php
require_once 'includes/functions.php';

$availableLeagues = [
    71 => 'Brasileirão Série A',
    72 => 'Copa do Brasil',
    13 => 'Libertadores',
    73 => 'Copa Sulamericana'
];

$availableSeasons = range(date('Y'), 2020);

$leagueId = filter_input(INPUT_GET, 'league', FILTER_VALIDATE_INT) ?: 71;
$season = filter_input(INPUT_GET, 'season', FILTER_VALIDATE_INT) ?: date('Y');
$teamName = filter_input(INPUT_POST, 'team_name', FILTER_SANITIZE_STRING) ?: null;

$leagueId = array_key_exists($leagueId, $availableLeagues) ? $leagueId : 71;
$season = in_array($season, $availableSeasons) ? $season : date('Y');

$nextRoundMatches = getMatches($leagueId, $season, 'next');
$lastResults = getMatches($leagueId, $season, 'last');
$teamMatches = $teamName ? getTeamMatchesByName($teamName, $leagueId, $season) : null;
$currentRound = getCurrentRound($leagueId, $season);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futebol|Resultados e Jogos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
    <script src="js/index.js" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><i class="fas fa-futbol"></i> Futebol </h1>
        </div>
    </header>

    <div class="filters-section">
        <div class="container">
            <form method="GET" class="filters-form">
                <div class="filters-wrapper">
                    <div class="filter-group">
                        <div class="select-wrapper">
                            <i class="fas fa-trophy select-icon"></i>
                            <select name="league" id="league" class="custom-select">
                                <?php foreach ($availableLeagues as $id => $name): ?>
                                    <option value="<?= $id ?>" <?= $leagueId == $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <div class="select-wrapper">
                            <i class="fas fa-calendar-alt select-icon"></i>
                            <select name="season" id="season" class="custom-select">
                                <?php foreach ($availableSeasons as $year): ?>
                                    <option value="<?= $year ?>" <?= $season == $year ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="filter-submit">
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="league-info-banner">
        <div class="container">
            <div class="league-stats">
                <div class="stat-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Temporada <?= $season ?></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-trophy"></i>
                    <span><?= htmlspecialchars($availableLeagues[$leagueId] ?? 'Campeonato') ?></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-stopwatch"></i>
                    <span>Rodada Atual: <?= $currentRound ?></span>
                </div>
            </div>
        </div>
    </div>

    <main class="container">
        <section class="section-container search-section">
            <h2><i class="fas fa-search"></i> Pesquisar Time</h2>
            <form method="POST" class="search-form">
                <div class="search-wrapper">
                    <input type="text" id="team_name" name="team_name" 
                           value="<?= htmlspecialchars($teamName ?? '') ?>" 
                           placeholder="Digite o nome do time"
                           list="team-suggestions">
                    <datalist id="team-suggestions">
                        <option value="Flamengo">
                        <option value="Palmeiras">
                        <option value="São Paulo">
                        <option value="Corinthians">
                    </datalist>
                    <button type="submit">
                        <i class="fas fa-search"></i>
                        Pesquisar
                    </button>
                </div>
                <div class="popular-teams">
                    <span>Times Populares:</span>
                    <div class="team-chips">
                        <button type="button" class="team-chip" onclick="setTeam('Corinthians')">Corinthians</button>
                        <button type="button" class="team-chip" onclick="setTeam('Palmeiras')">Palmeiras</button>
                        <button type="button" class="team-chip" onclick="setTeam('São Paulo')">São Paulo</button>
                    </div>
                </div>
            </form>
        </section>

        <?php if ($teamName): ?>
        <section class="section-container team-highlight">
            <h2><i class="fas fa-star"></i> Resultados para: <?= htmlspecialchars($teamName) ?></h2>
            <div class="team-stats">
                <?php
                $stats = getTeamStats($teamName, $leagueId, $season);
                if ($stats): ?>
                    <div class="stat-card">
                        <i class="fas fa-futbol"></i>
                        <span class="stat-value"><?= $stats['goals'] ?? 0 ?></span>
                        <span class="stat-label">Gols</span>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-trophy"></i>
                        <span class="stat-value"><?= $stats['victories'] ?? 0 ?></span>
                        <span class="stat-label">Vitórias</span>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-handshake"></i>
                        <span class="stat-value"><?= $stats['draws'] ?? 0 ?></span>
                        <span class="stat-label">Empates</span>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="loading-spinner">
            <div class="spinner"></div>
        </div>

        <div class="two-column-grid">
            <section class="section-container matches-section">
                <h2><i class="fas fa-forward"></i> Próximos Jogos</h2>
                <div class="matches-grid" id="next-matches">
                    <?php if (!empty($nextRoundMatches['response'])): ?>
                        <?php foreach ($nextRoundMatches['response'] as $key => $match): ?>
                            <div class="match-card" style="animation-delay: <?= $key * 0.1 ?>s">
                                <div class="match-header">
                                    <span class="match-status status-scheduled">
                                        <i class="far fa-clock"></i> Em breve
                                    </span>
                                    <span class="match-league">
                                        <i class="fas fa-trophy"></i> <?= htmlspecialchars($match['league']['name'] ?? 'Liga') ?>
                                    </span>
                                </div>
                                <div class="match-teams">
                                    <div class="team">
                                        <img src="<?= $match['teams']['home']['logo'] ?? 'path/to/default-logo.png' ?>" 
                                             alt="<?= htmlspecialchars($match['teams']['home']['name']) ?>" 
                                             class="team-logo">
                                        <div class="team-name">
                                            <?= htmlspecialchars($match['teams']['home']['name']) ?>
                                        </div>
                                    </div>
                                    <div class="match-time">
                                        <div class="match-date">
                                            <?= date('d/m', strtotime($match['fixture']['date'])) ?>
                                        </div>
                                        <div class="match-hour">
                                            <?= date('H:i', strtotime($match['fixture']['date'])) ?>
                                        </div>
                                    </div>
                                    <div class="team">
                                        <img src="<?= $match['teams']['away']['logo'] ?? 'path/to/default-logo.png' ?>" 
                                             alt="<?= htmlspecialchars($match['teams']['away']['name']) ?>" 
                                             class="team-logo">
                                        <div class="team-name">
                                            <?= htmlspecialchars($match['teams']['away']['name']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="match-footer">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($match['fixture']['venue']['name'] ?? 'Estádio não definido') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <i class="fas fa-calendar-times"></i>
                            <p>Nenhum jogo programado</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="section-container matches-section">
                <h2><i class="fas fa-history"></i> Últimos Resultados</h2>
                <div class="matches-grid" id="last-results">
                    <?php if (!empty($lastResults['response'])): ?>
                        <?php foreach ($lastResults['response'] as $key => $match): ?>
                            <div class="match-card" style="animation-delay: <?= $key * 0.1 ?>s">
                                <div class="match-header">
                                    <span class="match-status status-finished">
                                        <i class="fas fa-check"></i> Encerrado
                                    </span>
                                    <span class="match-league">
                                        <i class="fas fa-trophy"></i> <?= htmlspecialchars($match['league']['name'] ?? 'Liga') ?>
                                    </span>
                                </div>
                                <div class="match-teams">
                                    <div class="team <?= ($match['goals']['home'] > $match['goals']['away']) ? 'winner' : '' ?>">
                                        <img src="<?= $match['teams']['home']['logo'] ?? 'path/to/default-logo.png' ?>" 
                                             alt="<?= htmlspecialchars($match['teams']['home']['name']) ?>" 
                                             class="team-logo">
                                        <div class="team-name">
                                            <?= htmlspecialchars($match['teams']['home']['name']) ?>
                                        </div>
                                        <div class="team-score"><?= $match['goals']['home'] ?></div>
                                    </div>
                                    <div class="match-time">
                                        <div class="match-date">
                                            <?= date('d/m', strtotime($match['fixture']['date'])) ?>
                                        </div>
                                        <div class="score-divider">x</div>
                                    </div>
                                    <div class="team <?= ($match['goals']['away'] > $match['goals']['home']) ? 'winner' : '' ?>">
                                        <img src="<?= $match['teams']['away']['logo'] ?? 'path/to/default-logo.png' ?>" 
                                             alt="<?= htmlspecialchars($match['teams']['away']['name']) ?>" 
                                             class="team-logo">
                                        <div class="team-name">
                                            <?= htmlspecialchars($match['teams']['away']['name']) ?>
                                        </div>
                                        <div class="team-score"><?= $match['goals']['away'] ?></div>
                                    </div>
                                </div>
                                <div class="match-footer">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($match['fixture']['venue']['name'] ?? 'Estádio não definido') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <i class="fas fa-calendar-times"></i>
                            <p>Nenhum resultado disponível</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-futbol"></i> Futebol</h3>
                </div>
                <div class="footer-links">
                    <a href="#"><i class="fab fa-github"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Futebol. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <div class="floating-actions">
        <button id="scrollTop" class="action-button" title="Voltar ao topo">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button id="toggleTheme" class="action-button" title="Alternar tema">
            <i class="fas fa-moon"></i>
        </button>
    </div>
</body>
</html>