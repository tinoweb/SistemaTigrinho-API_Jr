import { create } from "zustand";

type LocalUserStore = {
  balance: number;
  isBeting: boolean;
  selectedColor: string;
  setSelectedColor: (color: string) => void;
  setIsBeting: (isBet: boolean) => void;
  increaseToBalance: (ammount: number) => void;
};

export const useLocalUserStore = create<LocalUserStore>((set) => ({
  balance: 500,
  isBeting: false,
  selectedColor: "red",
  setSelectedColor: (color) => {
    set({ selectedColor: color });
  },
  setIsBeting: (isBet) => {
    set({ isBeting: isBet });
  },
  increaseToBalance: (ammount) =>
    set((state) => ({
      balance: state.balance + ammount,
    })),
}));
