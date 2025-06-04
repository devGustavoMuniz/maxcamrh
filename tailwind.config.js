import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import animate from 'tailwindcss-animate'; // ADICIONE ESTA LINHA

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: "class", // CORRETO para Shadcn Vue
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue', // Garanta que este caminho cobre seus componentes Shadcn Vue
    // Se você copiou os componentes para, por exemplo, './resources/js/components/ui', está coberto.
  ],

  theme: {
    container: { // Configuração padrão do Shadcn Vue para container
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
      extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      // ADIÇÃO DA CONFIGURAÇÃO DE CORES, BORDAS, ETC. DO SHADCN VUE
      colors: {
        border: "var(--border)", // Remove hsl() daqui
        input: "var(--input)",   // Remove hsl() daqui
        ring: "var(--ring)",     // Remove hsl() daqui
        background: "var(--background)", // Remove hsl() daqui
        foreground: "var(--foreground)", // Remove hsl() daqui
        primary: {
          DEFAULT: "var(--primary)", // Remove hsl() daqui
          foreground: "var(--primary-foreground)", // Remove hsl() daqui
        },
        secondary: {
          DEFAULT: "var(--secondary)", // Remove hsl() daqui
          foreground: "var(--secondary-foreground)", // Remove hsl() daqui
        },
        destructive: {
          DEFAULT: "var(--destructive)", // Remove hsl() daqui
          foreground: "var(--destructive-foreground)", // Remove hsl() daqui
        },
        muted: {
          DEFAULT: "var(--muted)", // Remove hsl() daqui
          foreground: "var(--muted-foreground)", // Remove hsl() daqui
        },
        accent: {
          DEFAULT: "var(--accent)", // Remove hsl() daqui
          foreground: "var(--accent-foreground)", // Remove hsl() daqui
        },
        popover: {
          DEFAULT: "var(--popover)", // Remove hsl() daqui
          foreground: "var(--popover-foreground)", // Remove hsl() daqui
        },
        card: {
          DEFAULT: "var(--card)", // Remove hsl() daqui
          foreground: "var(--card-foreground)", // Remove hsl() daqui
        },
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
      keyframes: {
        "accordion-down": {
          from: { height: "0" }, // Em JS, use strings para valores CSS
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: "0" },
        },
        "collapsible-down": {
          from: { height: "0" },
          to: { height: 'var(--radix-collapsible-content-height)' },
        },
        "collapsible-up": {
          from: { height: 'var(--radix-collapsible-content-height)' },
          to: { height: "0" },
        },
        // Adicione outros keyframes se seus componentes Shadcn os usarem
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
        "collapsible-down": "collapsible-down 0.2s ease-out",
        "collapsible-up": "collapsible-up 0.2s ease-out",
      },
      // FIM DAS ADIÇÕES DO SHADCN VUE
    },
  },

  plugins: [
    forms,
    animate, // ADICIONE O PLUGIN ANIMATE AQUI
  ],
};
