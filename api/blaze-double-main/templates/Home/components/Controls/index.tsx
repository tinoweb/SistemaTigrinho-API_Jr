import { colors } from "@/constants";
import { Button } from "../Buttons";
import { useControls } from "./hooks/useControls";

export const Controls = () => {
  const {
    balance,
    bet,
    handleAmount,
    updateBet,
    selectedColor,
    setSelectedColor,
    setIsBeting,
    isBeting,
    isRunning,
  } = useControls();

  return (
    <div className="border border-gray-700 w-full p-5 md:w-1/3 flex items-center rounded-md">
      <div className="flex w-full flex-col gap-2">
        <div className="flex">
          <p className="px-3 text-sm py-2 rounded-full border border-gray-700">
            Saldo: <span className="font-bold">{balance}R$</span>
          </p>
        </div>
        <div className="flex flex-col gap-2">
          <label className="text-sm">Bet</label>
          <div className="flex gap-2">
            <input
              value={bet}
              onChange={handleAmount}
              type="text"
              className="border w-full bg-transparent border-gray-700 rounded-md p-2"
            />
            <button
              onClick={() => updateBet(bet * 2)}
              className="bg-neutral-900 text-white font-bold py-3 px-5 rounded-md"
            >
              2x
            </button>

            <button
              onClick={() => updateBet(bet / 2)}
              className="bg-neutral-900 text-white font-bold py-3 px-5 rounded-md"
            >
              1/2
            </button>
          </div>
        </div>
        <div className="flex justify-between gap-2">
          {colors.map(({ color, name, placeHolder }) => (
            <Button
              selected={selectedColor == name}
              onClick={() => setSelectedColor(name)}
              color={color}
              text={placeHolder}
            />
          ))}
        </div>
        <button
          onClick={() => setIsBeting(!isBeting)}
          disabled={isBeting || bet == 0 || !isRunning || bet > balance}
          className="bg-red-700 text-white font-bold py-3 px-5 rounded-md mt-5 disabled:opacity-50"
        >
          Entrar
        </button>
      </div>
    </div>
  );
};
