import js from "@eslint/js";
import vue from "eslint-plugin-vue";
import eslintConfigPrettier from "eslint-config-prettier";
import globals from "globals";

export default [
  {
    ignores: ["node_modules", "public/build", "vendor", "resources/js/ziggy.js"],
  },
  js.configs.recommended,
  ...vue.configs['flat/recommended'],
  eslintConfigPrettier,
  {
    files: ["resources/js/**/*.js", "resources/js/**/*.vue"],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
        route: "readonly",
      },
    },
    rules: {
      "vue/multi-word-component-names": "off",
    },
  },
];
