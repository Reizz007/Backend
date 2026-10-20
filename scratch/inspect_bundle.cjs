const https = require('https');

https.get('https://ekfee.vercel.app/assets/index-BGtrA_PR.js', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', () => {
        // Find apiUrl definition or baseUrl
        const idx = data.indexOf('/api/products');
        if (idx !== -1) {
            console.log('Snippet around /api/products:');
            console.log(data.substring(idx - 150, idx + 150));
        }

        const urls = data.match(/https?:\/\/[a-zA-Z0-9\.\-\:\/]+/g) || [];
        console.log('Unique URLs found:');
        console.log([...new Set(urls)]);
    });
});
