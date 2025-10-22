export function getSubObjectValue(data, column) {
    const columns = column.split("__");
    if(columns.length <= 1) return data[column]
    var result = data;
    columns.forEach((item, index) => {
       result = result[item]??''
    });
    return result;
}

export function drawBackground(overlay){
    const canvas = document.createElement('canvas')
    const size = 15
    const ctx = canvas.getContext('2d')

    const drawGrid = () => {
        canvas.width = window.innerWidth
        canvas.height = window.innerHeight

        for (let y = 0; y < canvas.height; y += size) {
        for (let x = 0; x < canvas.width; x += size) {
            if (Math.random() > 0.93) {
            ctx.fillStyle = 'rgba(230,230,230,0.4)'
            drawRoundedRect(ctx, x, y, size, size,8)
            }
        }
        }

        ctx.filter = 'blur(10px)'
    }

    drawGrid()

    canvas.classList.add('absolute', 'inset-0', 'pointer-events-none')
    overlay.appendChild(canvas)
    window.addEventListener('resize', () => {
        drawGrid()
    })
}

function drawRoundedRect (ctx, x, y, width, height, radius) {
    ctx.beginPath()
    ctx.moveTo(x + radius, y)
    ctx.lineTo(x + width - radius, y)
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius)
    ctx.lineTo(x + width, y + height - radius)
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height)
    ctx.lineTo(x + radius, y + height)
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius)
    ctx.lineTo(x, y + radius)
    ctx.quadraticCurveTo(x, y, x + radius, y)
    ctx.closePath()
    ctx.fill()
}
