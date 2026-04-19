<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Dosis</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        function calcular() {
            const superficie = parseFloat(document.getElementById('superficie').value) || 0;
            const dosi_ha = parseFloat(document.getElementById('dosi_ha').value) || 0;
            const volum_maquina = parseFloat(document.getElementById('volum_maquina').value) || 0;
            const volum_caldo_ha = parseFloat(document.getElementById('volum_caldo_ha').value) || 0;

            if (superficie && dosi_ha) {
                const total_producte = superficie * dosi_ha;
                document.getElementById('resultat_total').innerText = total_producte.toFixed(2) + " L/Kg";
                
                if (volum_caldo_ha) {
                    const total_caldo = superficie * volum_caldo_ha;
                    document.getElementById('resultat_caldo').innerText = total_caldo.toFixed(0) + " L";
                    
                    if (volum_maquina) {
                        const num_maquines = total_caldo / volum_maquina;
                        document.getElementById('resultat_maquines').innerText = num_maquines.toFixed(1);
                        
                        const producte_per_maquina = (volum_maquina * dosi_ha) / volum_caldo_ha;
                        document.getElementById('resultat_per_maquina').innerText = producte_per_maquina.toFixed(2) + " L/Kg";
                    }
                }
            }
        }
    </script>
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2><i class="fa-solid fa-calculator"></i> Calculadora de Dosis i Caldo</h2>
        <p class="page-subtitle">Eina per planificar les aplicacions fitosanitàries.</p>
    </div>

    <div class="panel mb-2">
        <div class="grid-2">
            <div>
                <label><i class="fa-solid fa-ruler-combined"></i> Superfície a tractar (ha)</label>
                <input type="number" id="superficie" step="0.01" oninput="calcular()" placeholder="0.00">
                
                <label><i class="fa-solid fa-flask"></i> Dosi recomanada (L o Kg / ha)</label>
                <input type="number" id="dosi_ha" step="0.01" oninput="calcular()" placeholder="0.00">
            </div>
            <div>
                <label><i class="fa-solid fa-fill-drip"></i> Volum de caldo desitjat (L / ha)</label>
                <input type="number" id="volum_caldo_ha" step="1" oninput="calcular()" placeholder="Ex: 1000">
                
                <label><i class="fa-solid fa-truck-front"></i> Capacitat dipòsit màquina (L)</label>
                <input type="number" id="volum_maquina" step="1" oninput="calcular()" placeholder="Ex: 2000">
            </div>
        </div>
    </div>

    <div class="panel" style="background: #f0fdf4; border-color: #bbf7d0;">
        <h3 class="panel-title" style="color: #166534;"><i class="fa-solid fa-square-poll-vertical"></i> Resultats del Càlcul</h3>
        <div class="grid-2">
            <div class="mb-2">
                <div class="label">Total Producte necessari:</div>
                <div class="valor" id="resultat_total" style="font-size: 1.5rem; color: #166534; font-weight: 800;">—</div>
            </div>
            <div class="mb-2">
                <div class="label">Total Caldo a preparar:</div>
                <div class="valor" id="resultat_caldo" style="font-size: 1.5rem; color: #166534; font-weight: 800;">—</div>
            </div>
            <div class="mb-2">
                <div class="label">Número de càrregues (màquines):</div>
                <div class="valor" id="resultat_maquines" style="font-size: 1.5rem; color: #166534; font-weight: 800;">—</div>
            </div>
            <div class="mb-2">
                <div class="label">Producte per cada càrrega:</div>
                <div class="valor" id="resultat_per_maquina" style="font-size: 1.5rem; color: #166534; font-weight: 800;">—</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
