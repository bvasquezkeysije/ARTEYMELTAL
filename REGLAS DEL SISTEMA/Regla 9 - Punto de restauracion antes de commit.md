# Regla 9 - Punto de restauración antes de commit al main

## Objetivo
Garantizar que siempre exista un punto de restauración del estado actual del servidor antes de realizar cualquier commit al branch main.

## Procedimiento obligatorio

1. **Verificar estado actual del servidor**
   git status
   git log --oneline -3

2. **Crear rama de respaldo** (si hay cambios pendientes)
   git checkout -b backup-YYYY-MM-DD
   git add .
   git commit -m "backup: estado del servidor antes de commit a main"
   git push origin backup-YYYY-MM-DD

3. **Solo después del respaldo**, proceder con el commit al main
   git checkout main
   git add .
   git commit -m "mensaje descriptivo del cambio"
   git push origin main

## Regla
**NUNCA** hacer push directo a main sin antes haber guardado un punto de restauración en una rama de backup.

## Nombre de rama de backup
Formato: backup-YYYY-MM-DD (ej: backup-2026-07-12)
