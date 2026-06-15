document.addEventListener('DOMContentLoaded', () => {
    initBarChart();
    initDonutChart();
});

function initBarChart() {
    const container = document.getElementById('dash-bar-chart');
    if (!container) return;

    let values = [42, 67, 55, 89, 73, 110, 95];
    let labels = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

    try {
        const rawVals = container.dataset.values;
        const rawLabels = container.dataset.labels;
        if (rawVals) values = JSON.parse(rawVals);
        if (rawLabels) labels = JSON.parse(rawLabels);
    } catch (_) { }

    const max = Math.max(...values);

    container.innerHTML = '';

    values.forEach((val, i) => {
        const heightPct = Math.round((val / max) * 90);

        const col = document.createElement('div');
        col.className = 'dash-bar-col';

        const bar = document.createElement('div');
        bar.className = 'dash-bar';
        bar.style.height = heightPct + 'px';
        bar.title = val + ' registros';

        const label = document.createElement('span');
        label.className = 'dash-bar-label';
        label.textContent = labels[i] ?? '';

        col.appendChild(bar);
        col.appendChild(label);
        container.appendChild(col);
    });
}

function initDonutChart() {
    const canvas = document.getElementById('dash-donut');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const cx = 60, cy = 60, r = 46, inner = 30;

    const slices = [
        { pct: parseFloat(canvas.closest('.dash-card')?.querySelector('.dash-leg-item:nth-child(1) .dash-leg-val')?.textContent) || 34, color: '#E8375A' },
        { pct: parseFloat(canvas.closest('.dash-card')?.querySelector('.dash-leg-item:nth-child(2) .dash-leg-val')?.textContent) || 52, color: '#F0997B' },
        { pct: parseFloat(canvas.closest('.dash-card')?.querySelector('.dash-leg-item:nth-child(3) .dash-leg-val')?.textContent) || 14, color: '#E5E7EB' },
    ];

    let start = -Math.PI / 2;

    slices.forEach(slice => {
        const angle = (slice.pct / 100) * Math.PI * 2;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, start, start + angle);
        ctx.closePath();
        ctx.fillStyle = slice.color;
        ctx.fill();
        start += angle;
    });

    ctx.beginPath();
    ctx.arc(cx, cy, inner, 0, Math.PI * 2);
    const bg = getComputedStyle(document.body).backgroundColor;
    ctx.fillStyle = bg.includes('rgb(249') ? '#FFFFFF' : bg;
    ctx.fill();
}
