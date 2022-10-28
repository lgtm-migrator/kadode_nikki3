/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: true,
    swcMinify: true,
    // beta版だけど良さそうなのでappDir使ってみる
    experimental: {
        appDir: true,
    },
};

module.exports = nextConfig;
