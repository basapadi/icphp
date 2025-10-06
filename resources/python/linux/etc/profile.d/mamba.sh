export MAMBA_ROOT_PREFIX="/home/bachtiarpanjaitan/Documents/bachtiarpanjaitan/ic-php/resources/python/linux"
__mamba_setup="$("/home/bachtiarpanjaitan/Documents/bachtiarpanjaitan/ic-php/resources/python/linux/bin/mamba" shell hook --shell posix 2> /dev/null)"
if [ $? -eq 0 ]; then
    eval "$__mamba_setup"
else
    alias mamba="/home/bachtiarpanjaitan/Documents/bachtiarpanjaitan/ic-php/resources/python/linux/bin/mamba"  # Fallback on help from mamba activate
fi
unset __mamba_setup
