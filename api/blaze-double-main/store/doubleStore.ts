import { create } from "zustand";

type UseDoubleStore = {
  bet: number;
  isBeting: boolean;
  isRunning: boolean;
  selectedColor: string;
  setSelectedColor: (color: string) => void;
  setIsRunning: (isRunning: boolean) => void;
  setIsBeting: (isBet: boolean) => void;
  setBet: (bet: number) => void;
};

export const useDoubleStore = create<UseDoubleStore>((set) => ({
  bet: 0,
  isBeting: false,
  selectedColor: "red",
  isRunning: false,
  setIsRunning: (isRunning) => {
    set({ isRunning });
  },
  setSelectedColor: (color) => {
    set({ selectedColor: color });
  },
  setIsBeting: (isBet) => {
    set({ isBeting: isBet });
  },
  setBet: (bet) => {
    set({ bet });
  },
}));
