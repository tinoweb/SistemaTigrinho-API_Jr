import { useDoubleStore } from "@/store/doubleStore";
import { useLocalUserStore } from "@/store/localUserStore";

export const useControls = () => {
  const balance = useLocalUserStore((state) => state.balance);
  const bet = useDoubleStore((state) => state.bet);
  const setBet = useDoubleStore((state) => state.setBet);
  const isBeting = useDoubleStore((state) => state.isBeting);
  const setIsBeting = useDoubleStore((state) => state.setIsBeting);
  const selectedColor = useDoubleStore((state) => state.selectedColor);
  const setSelectedColor = useDoubleStore((state) => state.setSelectedColor);
  const isRunning = useDoubleStore((state) => state.isRunning);

  const updateBet = (amount: number) => {
    if (isNaN(Number(amount))) return;
    if (Number(amount) < 0) return;
    if (Number(amount) > balance) return setBet(balance);

    setBet(amount);
  };
  const handleAmount = (e: any) => {
    updateBet(Number(e.target.value));
  };
  return {
    balance,
    bet,
    handleAmount,
    updateBet,
    selectedColor,
    setSelectedColor,
    setIsBeting,
    isBeting,
    isRunning,
  };
};
