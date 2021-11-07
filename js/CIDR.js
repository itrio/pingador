const regex = /^(\d{0,3})\.(\d{0,3})\.(\d{0,3})\.(\d{0,3}){1}\/(\d{1,2})$/;

class CIDR
{
    originalInput;

    octets;
    binaryOctets = [];
    fullBinaryIP;

    cidr;
    
    network;
    networkOctets;
    networkWithCIDR;
    binaryNetwork;
    fullBinaryNetwork;

    onlyNetworkBits;
    onlyHostsBits;

    qntNetworkBits;
    qntHostsBits;

    constructor(originalInput){
        this.originalInput = originalInput;

        if(this.isValid()){
            this.parse();
            return true;
        }

        return false;
    }

    isValid() {
        if(this.originalInput.match(regex) != null){
            return true;
        }

        return false;
    }

    parse() {
        this.splitInput();
        this.toBinary();
        this.parseNetwork();
        this.onlyHostsBits = this.fullBinaryIP.substring(this.cidr, this.fullBinaryIP.length);
        this.qntNetworkBits = parseInt(this.cidr);
        this.qntHostsBits = 32 - this.cidr;
    }

    splitInput(){
        var parse = regex.exec(this.originalInput);
        
        this.octets = [parse[1], parse[2], parse[3], parse[4]];
        this.cidr = parse[5];
    }

    toBinary(){
        this.octets.forEach(function(octet, index) {
            this.binaryOctets[index] = parseInt(octet).toString(2);
            this.binaryOctets[index] = this.putZerosLeft(this.binaryOctets[index], 8);
        }.bind(this));

        this.fullBinaryIP = this.binaryOctets.join("");
    }

    parseNetwork(){
        this.fullBinaryNetwork = "";

        for(var i = 0; i < 32; i++){
            if(i < parseInt(this.cidr)){
                this.fullBinaryNetwork += this.fullBinaryIP[i];
            }
            else{
                this.fullBinaryNetwork += "0";
            }
        }

        this.binaryNetwork = this.splitIP(this.fullBinaryNetwork);
        
        this.networkOctets = this.toIPFormat(this.binaryNetwork);
        this.network = this.networkOctets.join(".");
        this.networkWithCIDR = this.network + "/" + this.cidr;
        this.onlyNetworkBits = this.fullBinaryNetwork.substring(0, this.cidr);
    }

    toIPFormat(binaryIP){
        var ip = [];

        binaryIP.forEach(octet => {
            ip.push(parseInt(octet, 2).toString());
        });

        return ip;
    }

    getIPs(){
        var ips = [];

        for(var i = 1; i <= Math.pow(2, this.qntHostsBits) - 2; i++){
            var ip = this.onlyNetworkBits + this.putZerosLeft(i.toString(2), this.qntHostsBits);
            ips.push(this.toIPFormat(this.splitIP(ip)).join("."));
        }

        return ips;
    }

    putZerosLeft($string, $length){
        while($string.length < $length){
            $string = "0" + $string;
        }

        return $string;
    }

    splitIP(ip){
        return ip.split(/(\d{8})(\d{8})(\d{8})(\d{8})/g,)
            .filter(i => { return i; });
    }
}