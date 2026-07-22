import type { APIRoute } from 'astro';

export const GET: APIRoute = ({ site }) => {
  const baseUrl = site?.toString() || 'https://bandsawbladesupply.com/';
  return new Response(
    [
      'User-agent: *',
      'Allow: /',
      `Sitemap: ${new URL('/sitemap-index.xml', baseUrl).toString()}`
    ].join('\n'),
    {
      headers: {
        'Content-Type': 'text/plain; charset=utf-8'
      }
    }
  );
};
