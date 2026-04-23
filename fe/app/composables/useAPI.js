import { APISERVER } from "../const/api";

/**
 * useApi - a reusable async function for making API requests with optional authentication
 * @param {string} url - endpoint path to call (will be appended to baseURL)
 * @param {object} options - optional fetch options (method, headers, body, etc.)
 * @returns {Promise<{data: any, error: string|null}>} - returns response data or error message
 */
export const useApi = async (url, options = {}) => {
    const baseURL = `${APISERVER}`;

    // Retrieve token from localStorage if exists
    let token = localStorage.getItem("patient_token") || "";
    
    try {
        // Perform fetch using Nuxt $fetch with merged headers and options
        const data = await $fetch(baseURL + url, {
            ...options,
            headers: {
                Authorization: token ? `Bearer ${token}` : "",
                ...(options.headers || {}),
            },
        });
        return { data, error: null };
    } catch (err) {
        // Extract error message if available
        const message = err?.data?.message || err?.message || "API error";
        return { data: null, error: message };
    }
};
