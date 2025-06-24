import { ethers } from 'ethers';
import fs from 'fs';

const rpcUrl = process.env.POLYGON_RPC_URL;

async function main() {
  const [contractAddress, abiPath, privateKey, to, amount] = process.argv.slice(2);
  const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
  const provider = new ethers.JsonRpcProvider(rpcUrl);
  const wallet = new ethers.Wallet(privateKey, provider);
  const contract = new ethers.Contract(contractAddress, abi, wallet);
  const tx = await contract.transfer(to, BigInt(amount));
  await tx.wait();
  console.log(JSON.stringify({ txHash: tx.hash }));
}

main().catch((e) => { console.error(e); process.exit(1); });
