/**
 * Faz o download de um arquivo através de sua URL
 * @param {string} url - URL do arquivo para download
 * @param {string} filename - Nome do arquivo (opcional)
 */
export function downloadFile(url, filename = '') {
    if (!url) return;
    
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}