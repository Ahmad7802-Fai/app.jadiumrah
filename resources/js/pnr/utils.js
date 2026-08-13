export const rupiah = (n = 0) =>
    'Rp ' + Number(n).toLocaleString('id-ID');

export const serializeForm = (form) => {
    const data = {};
    new FormData(form).forEach((v, k) => data[k] = v);
    return data;
};
