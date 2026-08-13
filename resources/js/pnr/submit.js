import { PNRState } from './state';

export function submitPNR() {
    const error = PNRState.validate();
    if (error) {
        alert(error);
        return;
    }

    fetch('/ticketing/pnr/store-json', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(PNRState.payload())
    })
    .then(r => r.json())
    .then(res => window.location.href = res.redirect)
    .catch(() => alert('Gagal menyimpan PNR'));
}

// window.submitPNR = submitPNR;
// import { PNRState } from './state';

// export const submitPNR = () => {

//     const error = PNRState.validate();
//     if (error) {
//         alert(error);
//         return;
//     }

//     fetch('/ticketing/pnr', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document
//                 .querySelector('meta[name="csrf-token"]')
//                 ?.getAttribute('content')
//         },
//         body: JSON.stringify(PNRState.payload())
//     })
//     .then(r => {
//         if (!r.ok) throw new Error('Failed');
//         return r.json();
//     })
//     .then(res => {
//         window.location.href = `/ticketing/pnr/${res.id}`;
//     })
//     .catch(() => {
//         alert('Gagal menyimpan PNR');
//     });
// };

// /* expose */
// window.submitPNR = submitPNR;
