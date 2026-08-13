export const PNRState = {
    data: {
        pnr_code: null,
        airline_class: null,
        agent_id: null,
        client: null,
        pricing: null,
        sectors: []
    },

    setPNR(data) {
        this.data.pnr_code = data.pnr_code;
        this.render();
    },

    setClient(client) {
        this.data.client = client;
        this.render();
    },

    setPricing(pricing) {
        this.data.pricing = pricing;
        this.render();
    },

    setSectors(sectors) {
        this.data.sectors = sectors;
        this.render();
    },

    render() {
        document.getElementById('sum-pnr').innerText =
            this.data.pnr_code ?? '—';

        document.getElementById('sum-client').innerText =
            this.data.client?.name ?? '—';

        document.getElementById('sum-pricing').innerText =
            this.data.pricing
                ? `${this.data.pricing.pax} pax × Rp ${this.data.pricing.fare.toLocaleString('id-ID')}`
                : '—';

        document.getElementById('sum-sector').innerText =
            this.data.sectors.length
                ? `${this.data.sectors[0].origin} → ${this.data.sectors[0].destination}
                   (${this.data.sectors.length} sector)`
                : '—';
    },

    validate() {
        if (!this.data.client) return 'Client belum dipilih';
        if (!this.data.pricing) return 'Pricing belum diisi';
        if (!this.data.sectors.length) return 'Flight sector belum diisi';
        return null;
    },

    payload() {
        return {
            pnr_code: this.data.pnr_code,
            client_id: this.data.client.id,
            pax: this.data.pricing.pax,
            fare_per_pax: this.data.pricing.fare,
            deposit_per_pax: this.data.pricing.deposit,
            routes: this.data.sectors
        };
    }
};
// export const PNRState = {
//     data: {
//         pnr_code: null,
//         airline_class: null,
//         agent_id: null,

//         client: null,      // {id, name}
//         pricing: null,     // {pax, fare, deposit}
//         sectors: []        // [{origin, destination, date, flight}]
//     },

//     /* =============================
//      | SETTERS
//      ============================= */
//     setPNR(info) {
//         this.data.pnr_code = info.pnr_code;
//         this.data.airline_class = info.airline_class;
//         this.data.agent_id = info.agent_id ?? null;
//         this.render();
//     },

//     setClient(client) {
//         this.data.client = client;
//         this.render();
//     },

//     setPricing(pricing) {
//         this.data.pricing = pricing;
//         this.render();
//     },

//     setSectors(sectors) {
//         this.data.sectors = sectors;
//         this.render();
//     },

//     /* =============================
//      | SUMMARY RENDER
//      ============================= */
//     render() {
//         const el = (id) => document.getElementById(id);

//         el('sum-pnr').innerText =
//             this.data.pnr_code ?? '—';

//         el('sum-client').innerText =
//             this.data.client?.name ?? '—';

//         if (this.data.pricing) {
//             el('sum-pricing').innerText =
//                 `${this.data.pricing.pax} pax × Rp ${this.data.pricing.fare.toLocaleString('id-ID')}`;
//         } else {
//             el('sum-pricing').innerText = '—';
//         }

//         if (this.data.sectors.length) {
//             const s = this.data.sectors[0];
//             el('sum-sector').innerText =
//                 `${s.origin} → ${s.destination} (${this.data.sectors.length} sector)`;
//         } else {
//             el('sum-sector').innerText = '—';
//         }
//     },

//     /* =============================
//      | VALIDATION
//      ============================= */
//     validate() {
//         if (!this.data.client) return 'Client belum dipilih';
//         if (!this.data.pricing) return 'Pricing belum diisi';
//         if (!this.data.sectors.length) return 'Flight sector belum diisi';
//         return null;
//     },

//     /* =============================
//      | PAYLOAD
//      ============================= */
//     payload() {
//         return {
//             pnr_code: this.data.pnr_code,
//             airline_class: this.data.airline_class,
//             agent_id: this.data.agent_id,
//             client_id: this.data.client.id,

//             pax: this.data.pricing.pax,
//             fare_per_pax: this.data.pricing.fare,
//             deposit_per_pax: this.data.pricing.deposit,

//             routes: this.data.sectors
//         };
//     }
// };
