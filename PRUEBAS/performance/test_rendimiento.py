"""
Pruebas de rendimiento: carga, estrés y picos.
Utiliza requests + concurrent.futures para simular concurrencia.
Cubre los endpoints: /login, /dashboard y /api/reniec.
"""
import time
import statistics
import concurrent.futures
import requests
from config import BASE_URL


ENDPOINTS = {
    "login": f"{BASE_URL}/login",
    "dashboard": f"{BASE_URL}/dashboard",
    "reniec": f"{BASE_URL}/api/reniec?dni=00000000",
}


def measure_get(url):
    start = time.perf_counter()
    try:
        r = requests.get(url, timeout=10)
        elapsed = time.perf_counter() - start
        return {
            "url": url,
            "status": r.status_code,
            "elapsed": elapsed,
            "error": None,
        }
    except Exception as e:
        return {
            "url": url,
            "status": None,
            "elapsed": time.perf_counter() - start,
            "error": str(e),
        }


def run_scenario(name, users, requests_per_endpoint):
    print(f"\n=== {name} ===")
    all_results = []
    for endpoint_name, url in ENDPOINTS.items():
        results = []
        with concurrent.futures.ThreadPoolExecutor(max_workers=users) as executor:
            futures = [
                executor.submit(measure_get, url)
                for _ in range(requests_per_endpoint)
            ]
            for future in concurrent.futures.as_completed(futures):
                results.append(future.result())
        all_results.extend(results)
        _print_endpoint_stats(endpoint_name, users, requests_per_endpoint, results)
    _print_global_stats(all_results)
    return all_results


def _print_endpoint_stats(endpoint_name, users, requests_count, results):
    ok = [r for r in results if r["error"] is None]
    errors = [r for r in results if r["error"] is not None]
    times = [r["elapsed"] for r in ok]
    print(f"\nEndpoint: {endpoint_name}")
    print(f"  Usuarios: {users}, Peticiones: {requests_count}")
    print(f"  Exitosas: {len(ok)}, Errores: {len(errors)}")
    if times:
        print(
            f"  Min: {min(times):.3f}s, Max: {max(times):.3f}s, "
            f"Avg: {statistics.mean(times):.3f}s, "
            f"Median: {statistics.median(times):.3f}s"
        )


def _print_global_stats(results):
    ok = [r for r in results if r["error"] is None]
    errors = [r for r in results if r["error"] is not None]
    times = [r["elapsed"] for r in ok]
    total_time = sum(times) if times else 0
    throughput = len(ok) / total_time if total_time > 0 else 0
    print(f"\nResumen global:")
    print(f"  Total exitosas: {len(ok)}")
    print(f"  Total errores: {len(errors)}")
    print(f"  Throughput: {throughput:.2f} req/s")
    print(f"  Tasa de errores: {len(errors) / len(results) * 100:.2f}%")


if __name__ == "__main__":
    run_scenario("Prueba de carga", 10, 100)
    run_scenario("Prueba de estrés", 50, 500)
    run_scenario("Prueba de picos", 100, 300)
