/**
 * =====================================================
 * KANBAN DRAG & DROP — FINAL
 * =====================================================
 */

let draggedCard = null
let sourceColumn = null

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.kanban-card').forEach(card => {

        card.addEventListener('dragstart', (e) => {

            // 🔒 HARD GUARD
            if (card.dataset.locked === '1') {
                e.preventDefault()
                return
            }

            draggedCard = card
            sourceColumn = card.closest('.kanban-column')
            card.classList.add('is-dragging')

            e.dataTransfer.effectAllowed = 'move'
            e.dataTransfer.setData('text/plain', card.dataset.leadId)
        })

        card.addEventListener('dragend', () => {
            card.classList.remove('is-dragging')
            draggedCard = null
            sourceColumn = null
        })
    })

    document.querySelectorAll('.kanban-column').forEach(column => {

        column.addEventListener('dragover', (e) => {
            if (!draggedCard) return
            e.preventDefault()
            column.classList.add('is-over')
        })

        column.addEventListener('dragleave', () => {
            column.classList.remove('is-over')
        })

        column.addEventListener('drop', (e) => {
            e.preventDefault()
            column.classList.remove('is-over')

            if (!draggedCard || !sourceColumn) return
            if (column === sourceColumn) return

            // 🔒 DOUBLE CHECK
            if (draggedCard.dataset.locked === '1') return

            const leadId = draggedCard.dataset.leadId
            const pipelineId = column.dataset.pipelineId

            // 🔥 OPTIMISTIC UI
            column.appendChild(draggedCard)

            movePipeline(
                leadId,
                pipelineId,
                draggedCard,
                sourceColumn
            )
        })
    })
})

function movePipeline(leadId, pipelineId, cardEl, fromColumn) {

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
    .then(res => {
        if (!res.ok) throw new Error('Request failed')
        return res.json()
    })
    .then(res => {
        if (!res.success) throw new Error(res.message)

        // 🔒 LOCK CARD JIKA FINAL
        if (res.locked === true) {
            cardEl.dataset.locked = '1'
            cardEl.removeAttribute('draggable')
            cardEl.classList.add('kanban-card-disabled')
        }

        cardEl.classList.add('moved-success')
        setTimeout(() => cardEl.classList.remove('moved-success'), 600)

        showToast(res.message)
    })
    .catch(() => {
        // 🔙 ROLLBACK
        fromColumn.appendChild(cardEl)
        showToast('Gagal pindah pipeline', 'error')
    })
}

function showToast(message, type = 'success') {
    let toast = document.getElementById('toast')
    toast.className = `toast toast-${type}`
    toast.textContent = message
    toast.classList.add('show')
    setTimeout(() => toast.classList.remove('show'), 2500)
}
// =====================================================
// LOAD MORE BUTTON
// =====================================================    
document.addEventListener('click', e => {

    const btn = e.target.closest('.load-more-btn')
    if (!btn) return

    const pipelineId = btn.dataset.pipelineId
    const offset     = parseInt(btn.dataset.offset)

    btn.disabled = true
    btn.innerText = 'Loading...'

    fetch(`/cabang/leads-kanban/load-more?pipeline_id=${pipelineId}&offset=${offset}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(res => {

        const column = document.querySelector(
            `.kanban-column[data-pipeline-id="${pipelineId}"] .kanban-cards`
        )

        column.insertAdjacentHTML('beforeend', res.html)

        // update offset
        btn.dataset.offset = offset + res.count

        // hide button if no more
        if (res.count < 20) {
            btn.remove()
        } else {
            btn.disabled = false
            btn.innerText = 'Load more'
        }
    })
    .catch(err => {
        console.error(err)
        btn.disabled = false
        btn.innerText = 'Load more'
        showToast('Gagal load data', 'error')
    })
})

// /**
//  * =====================================================
//  * KANBAN DRAG & DROP — FINAL PRODUCTION VERSION
//  * =====================================================
//  * ✔ No reload
//  * ✔ Optimistic UI + rollback
//  * ✔ Locked lead (CLOSING / CLOSED / LOST) cannot be dragged
//  * ✔ Backend JSON only
//  * ✔ Safe guards (frontend + backend)
//  * =====================================================
//  */

// console.log('KANBAN JS LOADED')

// let draggedCard = null
// let sourceColumn = null

// document.addEventListener('DOMContentLoaded', () => {

//     /* =====================================================
//      | DRAGGABLE CARDS
//      ===================================================== */
//     document.querySelectorAll('.kanban-card').forEach(card => {

//         card.addEventListener('dragstart', (e) => {

//             // 🔒 HARD GUARD — LOCKED LEAD
//             if (card.dataset.locked === '1') {
//                 e.preventDefault()
//                 console.warn('DRAG BLOCKED (LOCKED LEAD):', card.dataset.leadId)
//                 return
//             }

//             draggedCard = card
//             sourceColumn = card.closest('.kanban-column')

//             card.classList.add('is-dragging')

//             e.dataTransfer.effectAllowed = 'move'
//             e.dataTransfer.setData('text/plain', card.dataset.leadId)

//             console.log('DRAG START', card.dataset.leadId)
//         })

//         card.addEventListener('dragend', () => {
//             card.classList.remove('is-dragging')
//             draggedCard = null
//             sourceColumn = null
//         })
//     })

//     /* =====================================================
//      | DROP ZONES (PIPELINE COLUMN)
//      ===================================================== */
//     document.querySelectorAll('.kanban-column').forEach(column => {

//         column.addEventListener('dragover', (e) => {
//             if (!draggedCard) return
//             e.preventDefault()
//             column.classList.add('is-over')
//         })

//         column.addEventListener('dragleave', () => {
//             column.classList.remove('is-over')
//         })

//         column.addEventListener('drop', (e) => {
//             e.preventDefault()
//             column.classList.remove('is-over')

//             if (!draggedCard || !sourceColumn) return

//             // 🔒 GUARD — LOCKED CARD DOUBLE CHECK
//             if (draggedCard.dataset.locked === '1') {
//                 console.warn('DROP BLOCKED (LOCKED LEAD)')
//                 return
//             }

//             // 🔒 GUARD — SAME COLUMN
//             if (column === sourceColumn) return

//             const leadId = draggedCard.dataset.leadId
//             const pipelineId = column.dataset.pipelineId

//             console.log('DROP', leadId, pipelineId)

//             // 🔥 1️⃣ OPTIMISTIC UI
//             column.querySelector('.kanban-cards')
//                 ?.appendChild(draggedCard) || column.appendChild(draggedCard)

//             // 🔥 2️⃣ BACKEND SYNC
//             movePipeline(
//                 leadId,
//                 pipelineId,
//                 draggedCard,
//                 sourceColumn
//             )
//         })
//     })
// })

// /* =====================================================
//  | AJAX MOVE PIPELINE
//  ===================================================== */
// function movePipeline(leadId, pipelineId, cardEl, fromColumn) {

//     fetch(`/cabang/leads/${leadId}/pipeline`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': document
//                 .querySelector('meta[name="csrf-token"]')
//                 .getAttribute('content')
//         },
//         body: JSON.stringify({ pipeline_id: pipelineId })
//     })
//     .then(res => {
//         if (!res.ok) throw new Error('Request failed')
//         return res.json()
//     })
//     .then(res => {
//         if (!res.success) throw new Error(res.message || 'Move failed')

//         // ✅ SUCCESS FEEDBACK
//         cardEl.classList.add('moved-success')
//         setTimeout(() => cardEl.classList.remove('moved-success'), 600)

//         showToast(res.message || 'Pipeline dipindahkan')
//     })
//     .catch(err => {
//         console.error('MOVE PIPELINE ERROR:', err)

//         // 🔙 ROLLBACK UI
//         if (fromColumn && cardEl) {
//             fromColumn.querySelector('.kanban-cards')
//                 ?.appendChild(cardEl) || fromColumn.appendChild(cardEl)
//         }

//         showToast('Gagal pindah pipeline', 'error')
//     })
// }

// /* =====================================================
//  | TOAST
//  ===================================================== */
// function showToast(message, type = 'success') {
//     let toast = document.getElementById('toast')

//     if (!toast) {
//         toast = document.createElement('div')
//         toast.id = 'toast'
//         document.body.appendChild(toast)
//     }

//     toast.className = `toast toast-${type}`
//     toast.textContent = message
//     toast.classList.add('show')

//     setTimeout(() => {
//         toast.classList.remove('show')
//     }, 2500)
// }
// /**
//  * =====================================================
//  * KANBAN DRAG & DROP (FINAL STABLE)
//  * =====================================================
//  * ✔ Optimistic UI
//  * ✔ Rollback aman
//  * ✔ Tidak false-error
//  * ✔ Sinkron backend
//  * =====================================================
//  */

// console.log('KANBAN JS LOADED')

// let draggedCard = null
// let sourceColumn = null

// document.addEventListener('DOMContentLoaded', () => {

//     /* =============================
//      | DRAG START / END
//      ============================= */
//     document.querySelectorAll('.kanban-card[draggable="true"]').forEach(card => {

//         card.addEventListener('dragstart', (e) => {
//             draggedCard = card
//             sourceColumn = card.closest('.kanban-column')

//             card.classList.add('is-dragging')

//             e.dataTransfer.effectAllowed = 'move'
//             e.dataTransfer.setData('text/plain', card.dataset.leadId)

//             console.log('DRAG START', card.dataset.leadId)
//         })

//         card.addEventListener('dragend', () => {
//             card.classList.remove('is-dragging')
//             draggedCard = null
//             sourceColumn = null
//         })

//     })

//     /* =============================
//      | DROP ZONE
//      ============================= */
//     document.querySelectorAll('.kanban-column').forEach(column => {

//         column.addEventListener('dragover', (e) => {
//             e.preventDefault()
//             column.classList.add('is-over')
//         })

//         column.addEventListener('dragleave', () => {
//             column.classList.remove('is-over')
//         })

//         column.addEventListener('drop', (e) => {
//             e.preventDefault()
//             column.classList.remove('is-over')

//             if (!draggedCard || !sourceColumn) return

//             const leadId = draggedCard.dataset.leadId
//             const pipelineId = column.dataset.pipelineId

//             console.log('DROP', leadId, pipelineId)

//             // 🧠 GUARD: drop di kolom yang sama
//             if (column === sourceColumn) return

//             // 🔥 1️⃣ OPTIMISTIC UI
//             column.appendChild(draggedCard)

//             // 🔥 2️⃣ BACKEND SYNC
//             movePipeline(
//                 leadId,
//                 pipelineId,
//                 draggedCard,
//                 sourceColumn
//             )
//         })

//     })

// })

// /* =============================
//  | AJAX MOVE PIPELINE
//  ============================= */
// function movePipeline(leadId, pipelineId, cardEl, fromColumn) {

//     fetch(`/cabang/leads/${leadId}/pipeline`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': document
//                 .querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ pipeline_id: pipelineId })
//     })
//     .then(res => res.json())
//     .then(res => {

//         if (!res.success) {
//             throw new Error(res.message || 'Move failed')
//         }

//         // ✅ FEEDBACK UI
//         if (cardEl) {
//             cardEl.classList.add('moved-success')
//             setTimeout(() => {
//                 cardEl?.classList.remove('moved-success')
//             }, 600)
//         }

//         showToast(res.message || 'Pipeline berhasil dipindahkan')
//     })
//     .catch(err => {
//         console.error(err)

//         // 🔙 ROLLBACK UI
//         if (fromColumn && cardEl) {
//             fromColumn.appendChild(cardEl)
//         }

//         showToast('Gagal pindah pipeline', 'error')
//     })
// }

// /* =============================
//  | TOAST
//  ============================= */
// function showToast(message, type = 'success') {
//     let toast = document.getElementById('toast')

//     if (!toast) {
//         toast = document.createElement('div')
//         toast.id = 'toast'
//         document.body.appendChild(toast)
//     }

//     toast.className = `toast toast-${type}`
//     toast.innerText = message
//     toast.classList.add('show')

//     setTimeout(() => {
//         toast.classList.remove('show')
//     }, 2500)
// }
// /**
//  * =====================================================
//  * KANBAN DRAG & DROP (PROFESSIONAL FINAL)
//  * =====================================================
//  * - No reload
//  * - DOM update langsung
//  * - Aman dari error null
//  * - Backend JSON only
//  * =====================================================
//  */
// console.log('KANBAN JS LOADED')

// let draggedCard = null
// let sourceColumn = null

// document.addEventListener('DOMContentLoaded', () => {

//     // =============================
//     // DRAG START / END
//     // =============================
//     document.querySelectorAll('.kanban-card[draggable="true"]').forEach(card => {

//         card.addEventListener('dragstart', (e) => {
//             draggedCard = card
//             sourceColumn = card.closest('.kanban-column')

//             card.classList.add('is-dragging')

//             e.dataTransfer.effectAllowed = 'move'
//             e.dataTransfer.setData('text/plain', card.dataset.leadId)

//             console.log('DRAG START', card.dataset.leadId)
//         })

//         card.addEventListener('dragend', () => {
//             card.classList.remove('is-dragging')
//             draggedCard = null
//             sourceColumn = null
//         })

//     })

//     // =============================
//     // DROP ZONE
//     // =============================
//     document.querySelectorAll('.kanban-column').forEach(column => {

//         column.addEventListener('dragover', (e) => {
//             e.preventDefault()
//             column.classList.add('is-over')
//         })

//         column.addEventListener('dragleave', () => {
//             column.classList.remove('is-over')
//         })

//         column.addEventListener('drop', (e) => {
//             e.preventDefault()
//             column.classList.remove('is-over')

//             if (!draggedCard) return

//             const leadId = draggedCard.dataset.leadId
//             const pipelineId = column.dataset.pipelineId

//             console.log('DROP', leadId, pipelineId)

//             // 🔥 1️⃣ PINDAHKAN CARD DI DOM (OPTIMISTIC UI)
//             column.appendChild(draggedCard)

//             // 🔥 2️⃣ KIRIM KE SERVER
//             movePipeline(leadId, pipelineId, column)
//         })

//     })

// })

// /* =============================
//  | AJAX MOVE PIPELINE (FINAL)
//  ============================= */
// function movePipeline(leadId, pipelineId, cardEl, fromColumn) {

//     fetch(`/cabang/leads/${leadId}/pipeline`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ pipeline_id: pipelineId })
//     })
//     .then(res => {
//         if (!res.ok) throw new Error('Request failed')
//         return res.json()
//     })
//     .then(res => {
//         if (!res.success) throw new Error(res.message)

//         // ✅ AMAN: cardEl TIDAK NULL
//         cardEl.classList.add('moved-success')

//         setTimeout(() => {
//             cardEl.classList.remove('moved-success')
//         }, 600)

//         showToast(res.message || 'Pipeline dipindahkan')
//     })
//     .catch(err => {
//         console.error(err)

//         // 🔙 ROLLBACK UI
//         if (fromColumn && cardEl) {
//             fromColumn.appendChild(cardEl)
//         }

//         showToast('Gagal pindah pipeline', 'error')
//     })
// }


// /* =============================
//  | TOAST
//  ============================= */
// function showToast(message, type = 'success') {
//     let toast = document.getElementById('toast')

//     if (!toast) {
//         toast = document.createElement('div')
//         toast.id = 'toast'
//         document.body.appendChild(toast)
//     }

//     toast.className = `toast toast-${type}`
//     toast.innerText = message
//     toast.classList.add('show')

//     setTimeout(() => {
//         toast.classList.remove('show')
//     }, 2500)
// }

// console.log('KANBAN JS LOADED')

// // =====================================================
// // GLOBAL STATE
// // =====================================================
// let draggedCard = null
// let draggedLeadId = null

// // =====================================================
// // DOM READY
// // =====================================================
// document.addEventListener('DOMContentLoaded', () => {

//     // =============================
//     // DRAG START
//     // =============================
//     document.addEventListener('dragstart', e => {
//         const card = e.target.closest('.kanban-card')
//         if (!card || card.getAttribute('draggable') !== 'true') return

//         draggedCard = card
//         draggedLeadId = card.dataset.leadId

//         card.classList.add('is-dragging')

//         console.log('DRAG START', draggedLeadId)
//     })

//     // =============================
//     // DRAG END
//     // =============================
//     document.addEventListener('dragend', () => {
//         if (draggedCard) {
//             draggedCard.classList.remove('is-dragging')
//         }

//         draggedCard = null
//         draggedLeadId = null
//     })

//     // =============================
//     // DROP ZONES
//     // =============================
//     document.querySelectorAll('.kanban-column').forEach(column => {

//         column.addEventListener('dragover', e => {
//             e.preventDefault()
//             column.classList.add('is-over')
//         })

//         column.addEventListener('dragleave', () => {
//             column.classList.remove('is-over')
//         })

//         column.addEventListener('drop', e => {
//             e.preventDefault()
//             column.classList.remove('is-over')

//             if (!draggedCard || !draggedLeadId) return

//             const pipelineId = column.dataset.pipelineId
//             if (!pipelineId) return

//             console.log('DROP', draggedLeadId, pipelineId)

//             movePipeline(draggedLeadId, pipelineId, column)
//         })
//     })
// })

// // =====================================================
// // AJAX: MOVE PIPELINE
// // =====================================================
// function movePipeline(leadId, pipelineId, targetColumn) {

//     fetch(`/cabang/leads/${leadId}/pipeline`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': document
//                 .querySelector('meta[name="csrf-token"]')
//                 ?.content || ''
//         },
//         body: JSON.stringify({
//             pipeline_id: pipelineId
//         })
//     })

//     .then(res => res.json())

//     .then(res => {

//         if (!res || !res.success) {
//             showToast(res?.message || 'Gagal pindah pipeline', 'error')
//             return
//         }

//         // =============================
//         // ✅ MOVE CARD DI DOM
//         // =============================
//         if (draggedCard && targetColumn) {
//             targetColumn.appendChild(draggedCard)
//         }

//         // visual feedback
//         draggedCard?.classList.add('moved-success')
//         setTimeout(() => {
//             draggedCard?.classList.remove('moved-success')
//         }, 700)

//         showToast(res.message || 'Pipeline berhasil dipindahkan')
//     })

//     .catch(err => {
//         console.error(err)
//         showToast('Gagal pindah pipeline', 'error')
//     })
// }

// // =====================================================
// // TOAST
// // =====================================================
// function showToast(message, type = 'success') {
//     let toast = document.getElementById('toast')

//     if (!toast) {
//         toast = document.createElement('div')
//         toast.id = 'toast'
//         document.body.appendChild(toast)
//     }

//     toast.textContent = message
//     toast.className = `toast show ${type}`

//     setTimeout(() => {
//         toast.className = 'toast'
//     }, 2500)
// }
