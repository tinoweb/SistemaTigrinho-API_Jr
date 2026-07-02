
import promisePool from "../database";
import path from "path";

const games = [
    { code: "fortune-ox", file: "fortune-ox/notcashox.ts" },
    { code: "fortune-tiger", file: "fortune-tiger/notcashtiger.ts" },
    { code: "fortune-mouse", file: "fortune-mouse/notcashmouse.ts" },
    { code: "fortune-rabbit", file: "fortune-rabbit/notcashrabbit.ts" },
    { code: "fortune-dragon", file: "fortune-dragon/notcashdragon.ts" },
    { code: "fortune-snake", file: "fortune-snake/notcashsnake.ts" },
    { code: "dragon-tiger-luck", file: "dragon-tiger-luck/notcashdragontigerluck.ts" },
    { code: "ganesha-gold", file: "ganesha-gold/notcashganesha.ts" },
    { code: "jungle-delight", file: "jungle-delight/notcashjungle.ts" },
    { code: "bikini-paradise", file: "bikini-paradise/notcashbikini.ts" },
    { code: "wild-bandito", file: "wild-bandito/notcashwildbandito.ts" },
    { code: "wild-bounty-sd", file: "wild-bounty-sd/notcashbouty.ts" },
    { code: "prosper-ftree", file: "prosper-ftree/notcashtree.ts" },
    { code: "rise-apollo", file: "rise-apollo/notcashriseapollo.ts" },
    { code: "chicky-run", file: "chicky-run/notcashchicky.ts" },
    { code: "double-fortune", file: "double-fortune/notcashdouble.ts" },
    { code: "majestic-ts", file: "majestic-ts/notcashmajestic.ts" },
    { code: "thai-river", file: "thai-river/notcashriver.ts" },
    { code: "ninja-raccoon", file: "ninja-raccoon/notcashraccoon.ts" },
    { code: "piggy-gold", file: "piggy-gold/notcashpiggy.ts" },
    { code: "lucky-clover", file: "lucky-clover/notcashclover.ts" },
    { code: "treasures-aztec", file: "treasures-aztec/notcashaztec.ts" },
    { code: "cash-mania", file: "cash-mania/notcashcash.ts" },
    { code: "btrfly-blossom", file: "btrfly-blossom/notcashbutterfly.ts" },
    { code: "wings-iguazu", file: "wings-iguazu/notcashiguazu.ts" },
    { code: "shaolin-soccer", file: "shaolin-soccer/notcashshaolin.ts" },
    { code: "three-cz-pigs", file: "three-cz-pigs/notcashthreecz.ts" },
    { code: "gdn-ice-fire", file: "gdn-ice-fire/notcashicefire.ts" },
    { code: "ultimate-striker", file: "ultimate-striker/notcashstriker.ts" },
    { code: "zombie-outbreak", file: "zombie-outbreak/notcashzombie.ts" },
];

async function populate() {
    console.log("Starting population of spins_inicial...");
    
    for (const game of games) {
        try {
            const filePath = path.join(__dirname, "../jsons", game.file);
            let module;
            try {
                module = require(filePath);
            } catch (e) {
                console.error(`Failed to require ${filePath}: ${e}`);
                continue;
            }
            
            const exportObj = module.default || module;
            let jsonFn = null;
            
            // Try to find the function dynamically
            for (const key in exportObj) {
                if (typeof exportObj[key] === 'function') {
                    jsonFn = exportObj[key];
                    break;
                }
            }
            
            if (!jsonFn) {
                console.error(`No function found in ${game.file}`);
                continue;
            }
            
            const json = await jsonFn(0, 0.05, 1); // Default values: saldo=0, cs=0.05, ml=1
            const jsonString = JSON.stringify(json);
            
            // Insert or Update
            await promisePool.query(
                "INSERT INTO spins_inicial (game_code, json) VALUES (?, ?) ON DUPLICATE KEY UPDATE json = ?",
                [game.code, jsonString, jsonString]
            );
            console.log(`Inserted/Updated ${game.code}`);
            
        } catch (error) {
            console.error(`Error processing ${game.code}:`, error);
        }
    }
    console.log("Finished population.");
    process.exit(0);
}

populate();
