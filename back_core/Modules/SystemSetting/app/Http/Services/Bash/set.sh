#!/bin/bash

if [[ "$1" == "--help" || "$1" == "-h" ]]; then
    echo "🔍 Usage: $0"
    echo
    echo "This script filters a pcap file based on a given number (IMSI, MSISDN)."
    echo
    echo "📌 Steps performed:"
    echo "  1. You enter an IMSI or MSISDN."
    echo "  2. The script finds all related identifiers (IMSI, MSISDN, TEIDs, SEIDs, etc.)."
    echo "  3. It generates individual files for each identifier type:"
    echo "     - HEX16: /tmp/final_hex16.txt"
    echo "     - HEX8 : /tmp/final_hex8.txt"
    echo "     - IMSI : /tmp/final_imsi.txt"
    echo "     - PHONE: /tmp/final_phone.txt"
    echo "     - TEL  : /tmp/final_tel.txt (format: tel:XXXXXXXXXXXXXX)"
    echo
    echo
    echo "  4. It generates a filter and applies it on the original PCAP to produce:"
    echo "     - Filtered PCAP: /tmp/filtered_output.pcap"
    echo
    echo "📥 Input:"
    echo "  - The script prompts you to enter a number to search (IMSI, MSISDN, TEID, SEID, etc.)."
    echo
    echo "🛠 Requirements:"
    echo "  - tshark must be installed and accessible"
    echo "  - Input PCAP must be located at: /home/siz-tel/mytrace/VoLTE.pcapng"
    echo
    echo "Examples:"
    echo "  $0 09123456789"
    echo "  $0 432890000000001"
    echo
    exit 0
fi













# ورودی‌های مورد نیاز
input_pcap="/home/siz-tel/trace/final.pcapng"
filtered_pcap="/tmp/filtered_output.pcap"
cleaned_file="/tmp/cleanned_final"
cleaned_file2="/tmp/cleanned_final2"
cleaned_file3="/tmp/cleanned_final3"

#read -p "Enter IMSI or MSISDN: " user_input
#-e gtpv2.f_teid_gre_key
# مرحله 1: گرفتن داده‌ها از tshark
sudo tshark -r "$input_pcap" -Y "s1ap.gTP_TEID || gtpv2.f_teid_gre_key || gtpv2.teid" -T fields \
       -e gtpv2.f_teid_gre_key -e s1ap.gTP_TEID -e gtpv2.teid  -e e212.imsi -e e164.msisdn  \
    | sed 's/^[[:space:]]*//;s/[[:space:]]*$//' \
    | tr -s ' \t' ',' \
    | sed 's/,$//' \
    | sed 's/\b0x0*\b\b//g; s/\b0+\b//g; s/,,*/,/g; s/^,//; s/,$//' \
    | sort \
    | awk -F',' 'NF>1' \
    > "$cleaned_file"
#-e nas-eps.emm.m_tmsi
# مرحله 1: گرفتن داده‌ها از tshark
sudo tshark -r "$input_pcap" -Y "s1ap || gtpv2 || gtp || pfcp.seid" -T fields \
    -e nas-eps.emm.m_tmsi -e e212.imsi  -e e164.msisdn -e pfcp.seid -e pfcp.f_teid.teid -e gtpv2.f_teid_gre_key\
    | sed 's/^[[:space:]]*//;s/[[:space:]]*$//' \
    | tr -s ' \t' ',' \
    | sed 's/,$//' \
    | sed 's/\b0x0*\b\b//g; s/\b0+\b//g; s/,,*/,/g; s/^,//; s/,$//' \
    | sort \
    | awk -F',' 'NF>1' \
    > "$cleaned_file2"

# مرحله 1: گرفتن داده‌ها از tshark
sudo tshark -r "$input_pcap" -Y "s1ap || gtpv2 || gtp || pfcp.seid || diameter" -T fields \
    -e e212.imsi  -e e164.msisdn -e diameter.hopbyhopid\
    | sed 's/^[[:space:]]*//;s/[[:space:]]*$//' \
    | tr -s ' \t' ',' \
    | sed 's/,$//' \
    | sed 's/\b0x0*\b\b//g; s/\b0+\b//g; s/,,*/,/g; s/^,//; s/,$//' \
    | sort \
    | awk -F',' 'NF>1' \
    > "$cleaned_file3"



#echo "✅ Cleaned file created at: $cleaned_file"
grep -Ev '(^|,)\s*0x[0-9a-fA-F]+.*(0x[0-9a-fA-F]+\s*,){1,}' $cleaned_file > /tmp/tmpfile && mv /tmp/tmpfile $cleaned_file
grep -Ev '(^|,)\s*0x[0-9a-fA-F]+.*(0x[0-9a-fA-F]+\s*,){1,}' $cleaned_file2 > /tmp/tmpfile && mv /tmp/tmpfile $cleaned_file2


# مرحله 2: دریافت عدد اولیه از کاربر و دنبال کردن اعداد مرتبط
#read -p "🔍 Enter number to search: " input_number
input_number=$1
related_file="/tmp/related_$input_number.txt"
related_file2="/tmp/related2_$input_number.txt"
related_file3="/tmp/related3_$input_number.txt"

# بررسی وجود عدد در فایل
if ! grep -q "\btel:$input_number\b" "$cleaned_file" && ! grep -q "\b$input_number\b" "$cleaned_file" ; then
     echo "❌ $input_number not found"
    exit 1
fi

touch /tmp/related_temp
echo "$input_number" > /tmp/related_temp

touch /tmp/related_temp2
echo "$input_number" > /tmp/related_temp2

touch /tmp/related_temp3
echo "$input_number" > /tmp/related_temp3

changed=true
while $changed; do
    changed=false
    cp /tmp/related_temp /tmp/related_old
    while IFS= read -r num; do
        grep "\b$num\b" "$cleaned_file" | tr ',' '\n' >> /tmp/related_temp

    done < /tmp/related_old
    sort -u /tmp/related_temp -o /tmp/related_temp
    if ! cmp -s /tmp/related_old /tmp/related_temp; then
        changed=true
    fi
done
cp /tmp/related_temp "$related_file"
rm /tmp/related_temp /tmp/related_old


changed=true
while $changed; do
    changed=false
    cp /tmp/related_temp2 /tmp/related_old2
    while IFS= read -r num; do
        grep "\b$num\b" "$cleaned_file2" | tr ',' '\n' >> /tmp/related_temp2

    done < /tmp/related_old2
    sort -u /tmp/related_temp2 -o /tmp/related_temp2
    if ! cmp -s /tmp/related_old2 /tmp/related_temp2; then
        changed=true
    fi
done


cp /tmp/related_temp2 "$related_file2"
rm /tmp/related_temp2 /tmp/related_old2


changed=true
while $changed; do
    changed=false
    cp /tmp/related_temp3 /tmp/related_old3
    while IFS= read -r num; do
        grep "\b$num\b" "$cleaned_file3" | tr ',' '\n' >> /tmp/related_temp3

    done < /tmp/related_old3
    sort -u /tmp/related_temp3 -o /tmp/related_temp3
    if ! cmp -s /tmp/related_old3 /tmp/related_temp3; then
        changed=true
    fi
done


cp /tmp/related_temp3 "$related_file3"
rm /tmp/related_temp3 /tmp/related_old3


# مرحله 3: دسته‌بندی اعداد در 4 فایل
hex16="/tmp/final_hex16.txt"
hex8="/tmp/final_hex8.txt"
imsi="/tmp/final_imsi.txt"
phone="/tmp/final_phone.txt"
tmsi="/tmp/final_tmsi.txt"
log_file="/home/siz-tel/trace/mme.log"
hopbyhopid="/tmp/final_hopbyhopid"

> "$hex16"
> "$hex8"
> "$imsi"
> "$phone"
> "$tmsi"
> "$hopbyhopid"

while IFS= read -r line; do
    [[ -z "$line" ]] && continue
    if [[ "$line" =~ ^0x[0-9a-fA-F]{16}$ ]]; then
        echo "$line" >> "$hex16"
        #short="0x$(printf '%08x' $((16#${line:2})))"
        #echo "$short" >> "$hex8"
    elif [[ "$line" =~ ^0x[0-9a-fA-F]{8}$ ]]; then
        echo "$line" >> "$hex8"
        #long="0x$(printf '%016x' $((16#${line:2})))"
       # echo "$long" >> "$hex16"
    elif [[ "$line" =~ ^[0-9]{15}$ ]]; then
        echo "$line" >> "$imsi"
    elif [[ "$line" =~ ^[0-9]{4}$ || "$line" =~ ^\+[0-9]{12}$ || "$line" =~ ^[0-9]{13}$ ]]; then
        echo "$line" >> "$phone"
    elif [[ "$line" =~ ^tel:[0-9]{13}$ || "$line" =~ ^tel:[0-9]{11}$ ]]; then
        echo "$line" >> "$tel"
    fi

done < "$related_file"

while IFS= read -r line; do
    [[ -z "$line" ]] && continue
    if [[ "$line" =~ ^0x[0-9a-fA-F]{16}$ ]]; then
        echo "$line" >> "$hex16"
        #short="0x$(printf '%08x' $((16#${line:2})))"
        #echo "$short" >> "$hex8"
    elif [[ "$line" =~ ^0x[0-9a-fA-F]{8}$ ]]; then
        echo "$line" >> "$hex8"
        #long="0x$(printf '%016x' $((16#${line:2})))"
       # echo "$long" >> "$hex16"
    elif [[ "$line" =~ ^[0-9]{15}$ ]]; then
        echo "$line" >> "$imsi"
    elif [[ "$line" =~ ^[0-9]{4}$ || "$line" =~ ^\+[0-9]{12}$ || "$line" =~ ^[0-9]{13}$ ]]; then
        echo "$line" >> "$phone"
    elif [[ "$line" =~ ^tel:[0-9]{13}$ || "$line" =~ ^tel:[0-9]{11}$ ]]; then
        echo "$line" >> "$tel"
    fi

done < "$related_file2"


while IFS= read -r line; do
    [[ -z "$line" ]] && continue
    if [[ "$line" =~ ^0x[0-9a-fA-F]{8}$ ]]; then
        echo "$line" >> "$hopbyhopid"
    fi

done < "$related_file3"

sort -u "$hex8" -o "$hex8"
sort -u "$hex16" -o "$hex16"
sort -u "$imsi" -o "$imsi"
sort -u "$phone" -o "$phone"
sort -u "$hopbyhopid" -o "$hopbyhopid"

#echo "📁 Output files:"
#echo "🔸 HEX16 : $hex16"
#echo "🔸 HEX8  : $hex8"
#echo "🔸 IMSI  : $imsi"
#echo "🔸 PHONE : $phone"

# مرحله 4: فیلتر کردن PCAP با استفاده از شناسه‌های مرتبط
full_filter=""
hex8_filter=""
pfcp_filter=""
s1ap=""
tmsi_filter=""
hop_filter=""

while read -r imsi_value; do
    grep "IMSI\[$imsi_value\]" "$log_file" | \
    grep -o "M_TMSI:0x[0-9a-fA-F]\+" | \
    sed 's/M_TMSI://' >> "$tmsi"
done < "$imsi"

while read -r hop; do
    [[ -z "$top" ]] && continue
    [[ -n "$hop_filter" ]] && hop_filter+=" || "
    hop_filter+="diameter.hopbyhopid == $hop"
done < "$hopbyhopid"


while read -r t; do
    [[ -z "$t" ]] && continue
    [[ -n "$hex8_filter" ]] && hex8_filter+=" || "
    hex8_filter+="gtpv2.f_teid_gre_key == $t"
done < "$hex8"


while read -r t; do
    [[ -z "$t" ]] && continue
    hex="${t#0x}"  # حذف "0x" از ابتدا
    # اطمینان از 8 رقمی بودن
    hex=$(printf "%08x" $((16#$hex)))
    # تبدیل به فرمتی مثل 00:00:06:b5
    s1ap_teid=$(echo "$hex" | sed 's/../&:/g;s/:$//')
    [[ -n "$s1ap" ]] && s1ap+=" || "
    s1ap+="s1ap.gTP_TEID == $s1ap_teid"
done < "$hex8"

while read -r t; do
    [[ -z "$t" ]] && continue
    [[ -n "$pfcp_filter" ]] && pfcp_filter+=" || "
    pfcp_filter+="pfcp.seid == $t"
done < "$hex16"


while read -r t; do
    [[ -z "$t" ]] && continue
    [[ -n "$pfcp_filter" ]] && pfcp_filter+=" || "
    pfcp_filter+="pfcp.f_teid.teid  == $t"
done < "$hex8"


# اضافه کردن IMSI ها
while read -r imsi_value; do
    [[ -z "$imsi_value" ]] && continue
    [[ -n "$full_filter" ]] && full_filter+=" || "
    full_filter+="e212.imsi == \"$imsi_value\""
done < "$imsi"

# اضافه کردن IMSI ها
while read -r imsi_value; do
    [[ -z "$imsi_value" ]] && continue
    [[ -n "$full_filter" ]] && full_filter+=" || "
    full_filter+="diameter.Subscription-Id-Data matches \"$imsi_value@ims.mnc089.mcc432.3gppnetwork.org\""
done < "$imsi"

#tmsi
while read -r imsi_value; do
    grep "IMSI\[$imsi_value\]" "$log_file" | \
    grep -o "M_TMSI:0x[0-9a-fA-F]\+" | \
    sed 's/M_TMSI://' >> "$tmsi"
    #full_filter+="nas-eps.emm.m_tmsi == \"$tmsi\""
done < "$imsi"
while read -r tmsi_value; do
    [[ -z "$tmsi_value" ]] && continue
    [[ -n "$full_filter" ]] && full_filter+=" || "
    full_filter+="nas-eps.emm.m_tmsi == $tmsi_value"
done < "$tmsi"



# اضافه کردن IMSI ها
#while read -r phone_value; do
#    [[ -z "$phone_value" ]] && continue
#    [[ -n "$full_filter" ]] && full_filter+=" || "
    #full_filter+="sip.Request-Line == \"INVITE sip:$phone_value@ims.mnc089.mcc432.3gppnetwork.org;user=phone SIP/2.0\""
#     full_filter+="sip.to.addr == \"tel:$phone_value;phone-context=ims.mnc089.mcc432.3gppnetwork.org\""

#done < "$phone"
#tshark -r amir3.pcap -d tcp.port==3871,diameter -Y 'diameter.Subscription-Id-Data == "sip:432890000000001@ims.mnc089.mcc432.3gppnetwork.org"'


# اضافه کردن شماره‌های تلفن
while read -r phone_value; do
    [[ -z "$phone_value" ]] && continue
    [[ -n "$full_filter" ]] && full_filter+=" || "
    full_filter+="e164.msisdn == \"$phone_value\""
done < "$phone"

while read -r tel_value; do
    [[ -z "$tel_value" ]] && continue
    [[ -n "$full_filter" ]] && full_filter+=" || "
    full_filter+="sip.from.addr == \"$tel_value\""
done < "$phone"


[[ -n "$hex8_filter" ]] && full_filter+=" || $hex8_filter"
[[ -n "$s1ap" ]] && full_filter+=" || $s1ap"
[[ -n "$pfcp_filter" ]] && full_filter+=" || $pfcp_filter"
#echo"########################################################"
echo "$full_filter"
#echo"########################################################"

sudo tshark -r "$input_pcap" -d tcp.port==3871,diameter -Y "$full_filter" -w "$filtered_pcap"
#-d udp.port==4060,sip  -d udp.port==6060,sip
if [[ "$?" -eq 0 ]]; then
  echo "✅ Filtered PCAP written to: $filtered_pcap"
else
  echo "❌ Filtered PCAP Does Not written"
fi
