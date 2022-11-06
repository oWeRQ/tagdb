export default function(response) {
    if (response.data) {
        const data = response.data.data;
        data.meta = response.data.meta;
        return data;
    }
};
