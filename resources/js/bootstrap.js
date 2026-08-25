/**
 * AMBARELLA global client bootstrap.
 * Kept as an explicit entry point for future integrations (axios, analytics…).
 */

import axios from 'axios';

window.axios = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
    },
});
