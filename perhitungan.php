<?php 
class perhitungan {
    // x3 = umur, x2 = gaji, x1 = nilai jaminan, x4 = nilai milik sendiri 
    public function variabelX3($umur,$batasUmur){
            return $umur >= $batasUmur ? 1 : 0;
    }
    
    public function variabelX4($pengajuan, $sisaAsset)
    {
        return $pengajuan >= $sisaAsset ? 1 : 0;
    }
    public function variabelX2($nilaiPinjaman,$bunggaKPR, $tenor, $batasMinimTungakkan, $gajiBersih)
    {
    
        $dataA = ($nilaiPinjaman * $bunggaKPR / 100) + ($nilaiPinjaman /  $tenor) * $batasMinimTungakkan;
        $dataB = $gajiBersih/ $batasMinimTungakkan; 
        return $dataA > $dataB ? 1 :0; 
    }   

    public function variableX1($nilaiPinjaman, $nilaiJaminan){
        $dataA =  $nilaiJaminan / 2;

        if($nilaiPinjaman < $dataA){
            $nilaiCoverages = $this->nilaiCoverage($nilaiJaminan,$nilaiPinjaman);
            return $nilaiCoverages;
        } else{
            return 0;
        }

    }

    public function nilaiCoverage($nilaiJaminan, $nilaiPinjaman){
        $dataA =  $nilaiPinjaman / $nilaiJaminan * 100;
        return $dataA;
    }
    public function convertTo($nilaiJaminan, $nilaiPinjaman)
    {
        $dataA =  $nilaiPinjaman / $nilaiJaminan * 100;
        return $dataA;
    }

}
