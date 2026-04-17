# THC Detox Calculator (WordPress Plugin)

Plugin moderno para WordPress que estima una ventana orientativa de detección de THC en orina y sangre con enfoque conservador.

> **Disclaimer obligatorio**: Esta herramienta ofrece una estimación aproximada y no garantiza un resultado negativo en un test de drogas.

## Estructura de carpetas

```text
thc-detox-calculator/
├── thc-detox-calculator.php
├── README.md
├── includes/
│   ├── class-thc-detox-calculator-plugin.php
│   ├── class-thc-detox-calculator-shortcode.php
│   ├── class-thc-detox-calculator-estimator.php
│   └── class-thc-detox-calculator-calendar.php
├── templates/
│   └── calculator-form.php
└── assets/
    ├── css/
    │   └── thc-detox-calculator.css
    └── js/
        └── thc-detox-calculator.js
```

## Instalación exacta

1. Copia la carpeta `thc-detox-calculator` en `wp-content/plugins/`.
2. Entra a **WP Admin → Plugins**.
3. Activa **THC Detox Calculator**.
4. Inserta el shortcode en una página o entrada:

```text
[thc_detox_calculator]
```

## Cómo ampliar el algoritmo en el futuro

- Ajustar reglas base por frecuencia en `includes/class-thc-detox-calculator-estimator.php` (`$base_days`).
- Ajustar multiplicadores (cantidad, potencia, ritmo corporal, movimiento) en `build_multiplier()`.
- Añadir nuevos factores con bajo peso manteniendo el enfoque conservador.
- Incorporar nuevos textos de influencia en `build_influences()` sin prometer precisión clínica.
- Mantener la separación actual: validación y cálculo en PHP, UX dinámica en JS.

## Referencias usadas para modelado heurístico (documentación interna)

- Zamnesia THC Detox Calculator
- Healthline: *How Long Does Weed Stay in Your System?* (2026)
- NCBI StatPearls: *Cannabis Use Disorder*
- PMC: *Extended Urinary Δ9-Tetrahydrocannabinol Excretion in Chronic Cannabis Users...*
- HHS / Federal Register: cutoffs de workplace testing para metabolito THC

Este plugin **no** sustituye asesoramiento médico, legal o forense.
