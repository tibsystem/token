import { ethers } from 'ethers';
import fs from 'fs';

const rpcUrl = process.env.POLYGON_RPC_URL;

async function main() {
  const [contractAddress, abiPath, investorKey, relayerKey, to, amount] = process.argv.slice(2);
  if (!contractAddress || !abiPath || !investorKey || !relayerKey || !to || !amount) {
    console.error('Usage: node relay_meta_transfer.js <contractAddress> <abiPath> <investorKey> <relayerKey> <to> <amount>');
    process.exit(1);
  }

  const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
  const provider = new ethers.JsonRpcProvider(rpcUrl);
  const investorWallet = new ethers.Wallet(investorKey, provider);
  const relayerWallet = new ethers.Wallet(relayerKey, provider);
  const contract = new ethers.Contract(contractAddress, abi, provider);

  const nonce = await contract.nonces(investorWallet.address);
  const messageHash = ethers.solidityPackedKeccak256(
    ['address', 'address', 'address', 'uint256', 'uint256'],
    [contractAddress, investorWallet.address, to, amount, nonce]
  );

  const signature = await investorWallet.signMessage(ethers.getBytes(messageHash));
  const sig = ethers.Signature.from(signature);

  const relayerContract = contract.connect(relayerWallet);
  const tx = await relayerContract.metaTransfer(
    investorWallet.address,
    to,
    amount,
    sig.v,
    sig.r,
    sig.s
  );
  await tx.wait();
  console.log(JSON.stringify({ txHash: tx.hash }));
}

main().catch((e) => { console.error(e); process.exit(1); });
