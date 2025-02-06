document.addEventListener('DOMContentLoaded', function() {
    // Adicionar constantes no topo
    const CONFIG = {
        rowsPerPage: 3,
        spinnerDelay: 300,
        debounceDelay: 250
    };

    // Adicionar debounce para melhor performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    class TablePagination {
        constructor(tableId, controlsId) {
            this.table = document.getElementById(tableId);
            this.controls = document.getElementById(controlsId);
            this.rows = this.table?.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr') || [];
            this.totalPages = Math.ceil(this.rows.length / CONFIG.rowsPerPage);
            this.currentPage = 1;
            
            this.init();
        }

        init() {
            if (!this.table || !this.controls) return;
            this.displayPage(1);
            this.addEventListeners();
        }

        displayPage(page) {
            const start = (page - 1) * CONFIG.rowsPerPage;
            const end = start + CONFIG.rowsPerPage;

            Array.from(this.rows).forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            this.updateControls();
        }

        updateControls() {
            this.controls.innerHTML = '';
            
            for (let i = 1; i <= this.totalPages; i++) {
                const button = document.createElement('button');
                button.innerText = i;
                button.className = `pagination-button ${i === this.currentPage ? 'active' : ''}`;
                button.setAttribute('aria-label', `Página ${i}`);
                button.addEventListener('click', () => this.goToPage(i));
                this.controls.appendChild(button);
            }
        }

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.displayPage(page);
        }

        addEventListeners() {
            // Adicionar navegação por teclado
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.goToPage(this.currentPage - 1);
                if (e.key === 'ArrowRight') this.goToPage(this.currentPage + 1);
            });
        }
    }

    // Inicializar paginação
    new TablePagination('next-matches-table', 'pagination-controls');

    const scrollBtn = document.getElementById('scrollTop');
    scrollBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    const themeBtn = document.getElementById('toggleTheme');
    themeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme');
        const icon = themeBtn.querySelector('i');
        icon.classList.toggle('fa-moon');
        icon.classList.toggle('fa-sun');
    });

    window.setTeam = function(teamName) {
        document.getElementById('team_name').value = teamName;
        document.querySelector('.search-form').submit();
    }
});