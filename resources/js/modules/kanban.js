let draggedCard = null
let draggedLeadId = null

document.addEventListener('dragstart', function (e) {
    const card = e.target.closest('.kanban-card')
    if (!card) return

    draggedCard = card
    card.classList.add('dragging')

    e.dataTransfer.effectAllowed = 'move'
    e.dataTransfer.setData('text/plain', card.dataset.leadId)

    console.log('DRAG START', card.dataset.leadId)
})

document.addEventListener('dragend', function (e) {
    const card = e.target.closest('.kanban-card')
    if (!card) return

    card.classList.remove('dragging')
    draggedCard = null
})

document.addEventListener('dragover', function (e) {
    const column = e.target.closest('.kanban-column')
    if (!column) return

    e.preventDefault()
    column.classList.add('is-over')

})

document.addEventListener('dragleave', function (e) {
    const column = e.target.closest('.kanban-column')
    if (!column) return

    column.classList.remove('is-over')

})

document.addEventListener('drop', function (e) {
    const column = e.target.closest('.kanban-column')
    if (!column || !draggedCard) return

    e.preventDefault()
    column.classList.remove('is-over')


    column.appendChild(draggedCard)

    const leadId = draggedCard.dataset.leadId
    const pipelineId = column.dataset.pipelineId

    console.log('DROP', leadId, pipelineId)

    movePipeline(leadId, pipelineId)
})

// AJAX: MOVE PIPELINE
function movePipeline(leadId, pipelineId) {

    console.log('MOVE PIPELINE', leadId, pipelineId)

    fetch(`/cabang/leads/${leadId}/pipeline`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ pipeline_id: pipelineId })
    })
    .then(res => res.json()) // ❗ JANGAN pakai res.ok
    .then(res => {

        console.log('SERVER RESPONSE', res)

        if (res.success !== true) {
            showToast(res.message || 'Gagal pindah pipeline', 'error')
            return
        }

        // ✅ SUCCESS UI
        showToast(res.message || 'Pipeline dipindahkan')

    })
    .catch(err => {
        console.error('JS ERROR', err)
        showToast('Kesalahan Javascript', 'error')
    })
}

// function movePipeline(leadId, pipelineId) {

//     fetch(`/cabang/leads/${leadId}/pipeline`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Accept': 'application/json', // 🔥 PENTING
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ pipeline_id })
//     })

//     .then(res => {
//         if (!res.ok) throw new Error('Request failed')
//         return res.json()
//     })
//     .then(res => {
//         if (!res.success) throw new Error(res.message || 'Move failed')

//         // ✅ VISUAL FEEDBACK
//         draggedCard.classList.add('moved-success')

//         setTimeout(() => {
//             draggedCard.classList.remove('moved-success')
//         }, 800)

//         // OPTIONAL: toast
//         showToast(res.message || 'Pipeline dipindahkan')
//     })
//     .catch(err => {
//         console.error(err)
//         showToast('Gagal pindah pipeline', 'error')
//     })
// }

// TOAST FUNCTION
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast')
    toast.textContent = message
    toast.className = 'toast show ' + (type === 'error' ? 'error' : '')

    setTimeout(() => {
        toast.className = 'toast'
    }, 2500)
}
