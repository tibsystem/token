import { ethers } from 'ethers';
import fs from 'fs';

const rpcUrl = process.env.POLYGON_RPC_URL;

async function main() {
  const [contractAddress, abiPath, ownerKey, investorsCsv, amountsCsv] = process.argv.slice(2);
  if (!contractAddress || !abiPath || !ownerKey || !investorsCsv || !amountsCsv) {
    console.error('Usage: node admin_buyback.js <contractAddress> <abiPath> <ownerKey> <investorsCsv> <amountsCsv>');
    process.exit(1);
  }
  const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
  const provider = new ethers.JsonRpcProvider(rpcUrl);
  const wallet = new ethers.Wallet(ownerKey, provider);
  const contract = new ethers.Contract(contractAddress, abi, wallet);
  const investors = investorsCsv.split(',');
  const amounts = amountsCsv.split(',').map((a) => BigInt(a));
  const tx = await contract.adminBuyback(investors, amounts);
  await tx.wait();
  console.log(JSON.stringify({ txHash: tx.hash }));
}

main().catch((e) => { console.error(e); process.exit(1); });
