import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import {
  tempo_leitura,
  url_da_imagem,
  normalizar_origem_conteudo,
  normalizar_texto,
  analisar_contraste_imagem,
} from "../../resources/js/helpers";

describe("helpers.js", () => {
  describe("normalizar_texto", () => {
    it("remove acentos e converte para minúsculas", () => {
      expect(normalizar_texto("Programação")).toBe("programacao");
      expect(normalizar_texto("  AÇÃO  ")).toBe("acao");
      expect(normalizar_texto("")).toBe("");
      expect(normalizar_texto(null)).toBe("");
    });
  });

  describe("tempo_leitura", () => {
    it("calcula o tempo de leitura com valor mínimo de 1 minuto", () => {
      expect(tempo_leitura("Olá mundo")).toBe(1);
      expect(tempo_leitura("")).toBe(1);
    });

    it("calcula aproximadamente 200 palavras por minuto", () => {
      const texto_longo = Array(400).fill("palavra").join(" ");
      expect(tempo_leitura(texto_longo)).toBe(2);
    });
  });

  describe("url_da_imagem", () => {
    it("retorna URL correta do post", () => {
      expect(url_da_imagem({ image: "capa-123.jpg", uuid: "abc-456" })).toBe(
        "/post/image/capa-123.jpg"
      );
      expect(url_da_imagem({ image: null, uuid: "abc-456" })).toBe(
        "/post/image/abc-456"
      );
      expect(url_da_imagem({ image: "https://externo.com/foto.jpg", uuid: "abc-456" })).toBe(
        "https://externo.com/foto.jpg"
      );
    });
  });

  describe("normalizar_origem_conteudo", () => {
    it("retorna texto original quando não há url pública ou texto", () => {
      expect(normalizar_origem_conteudo("", "https://exemplo.com")).toBe("");
      expect(normalizar_origem_conteudo(null, "https://exemplo.com")).toBe(null);
    });
  });

  describe("analisar_contraste_imagem", () => {
    it("retorna fallback escuro quando a imagem é inválida ou nula", async () => {
      const resultado = await analisar_contraste_imagem(null);
      expect(resultado).toEqual({
        eh_escura: true,
        luminancia: 0,
        r: 0,
        g: 0,
        b: 0,
      });
    });

    it("identifica imagem escura corretamente via canvas", async () => {
      // Mock de elemento de imagem completo
      const img_mock = {
        complete: true,
        naturalWidth: 100,
        naturalHeight: 100,
      };

      // Mock de canvas com pixels escuros (R=30, G=30, B=30)
      const fake_pixel_data = new Uint8ClampedArray(100 * 60 * 4);
      for (let i = 0; i < fake_pixel_data.length; i += 4) {
        fake_pixel_data[i] = 30; // R
        fake_pixel_data[i + 1] = 30; // G
        fake_pixel_data[i + 2] = 30; // B
        fake_pixel_data[i + 3] = 255; // A
      }

      const get_context_spy = vi.spyOn(HTMLCanvasElement.prototype, "getContext").mockReturnValue({
        drawImage: vi.fn(),
        getImageData: vi.fn().mockReturnValue({ data: fake_pixel_data }),
      });

      const resultado = await analisar_contraste_imagem(img_mock);
      expect(resultado.eh_escura).toBe(true);
      expect(resultado.luminancia).toBe(30);

      get_context_spy.mockRestore();
    });

    it("identifica imagem clara corretamente via canvas", async () => {
      const img_mock = {
        complete: true,
        naturalWidth: 100,
        naturalHeight: 100,
      };

      // Mock de canvas com pixels claros (R=240, G=240, B=240)
      const fake_pixel_data = new Uint8ClampedArray(100 * 60 * 4);
      for (let i = 0; i < fake_pixel_data.length; i += 4) {
        fake_pixel_data[i] = 240;
        fake_pixel_data[i + 1] = 240;
        fake_pixel_data[i + 2] = 240;
        fake_pixel_data[i + 3] = 255;
      }

      const get_context_spy = vi.spyOn(HTMLCanvasElement.prototype, "getContext").mockReturnValue({
        drawImage: vi.fn(),
        getImageData: vi.fn().mockReturnValue({ data: fake_pixel_data }),
      });

      const resultado = await analisar_contraste_imagem(img_mock);
      expect(resultado.eh_escura).toBe(false);
      expect(resultado.luminancia).toBe(240);

      get_context_spy.mockRestore();
    });
  });
});
